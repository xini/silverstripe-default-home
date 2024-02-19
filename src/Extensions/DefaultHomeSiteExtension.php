<?php

namespace Innoweb\DefaultHome\Extensions;

use SilverStripe\CMS\Controllers\RootURLController;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\CMS\Model\SiteTreeExtension;
use SilverStripe\Core\Manifest\ModuleLoader;
use SilverStripe\ORM\DB;

class DefaultHomeSiteExtension extends SiteTreeExtension
{
    public function onAfterWrite()
    {
        $siteClass = null;
        $manifest = ModuleLoader::inst()->getManifest();
        if ($manifest->moduleExists('symbiote/silverstripe-multisites')) {
            $siteClass = \Symbiote\Multisites\Model\Site::class;
        }
        if ($manifest->moduleExists('fromholdio/silverstripe-configured-multisites')) {
            $siteClass = \Fromholdio\ConfiguredMultisites\Model\Site::class;
        }

        if (empty($siteClass)) {
            return;
        }

        $sites = $siteClass::get();
        if ($sites->count() < 1) {
            return;
        }

        $homeClass = RootURLController::config()->get('default_homepage_class');
        if (!$homeClass) {
            throw new \UnexpectedValueException(
                'RootURLController requires the config var $default_homepage_class to be declared. '
                . 'Nothing set.'
            );
        }
        if (!is_a($homeClass, SiteTree::class, true)) {
            throw new \UnexpectedValueException(
                'RootURLController requires the config var $default_homepage_class to be declared '
                . 'and that var must be the name of a valid SiteTree subclass. Supplied value "' . $homeClass
                . '" is not a valid subclass of SiteTree.'
            );
        }

        $homeLink = RootURLController::get_homepage_link();

        foreach ($sites as $site) {
            $homePage = SiteTree::get()
                ->filter([
                    'ParentID' => $site->ID,
                    'URLSegment' => $homeLink
                ])
                ->first();
            if ($homePage) {
                if ($homePage->ClassName !== $homeClass) {
                    $currentClass = $homePage->ClassName;
                    $currentTitle = $homePage->Title;
                    $homePage->ClassName = $homeClass;
                    $homePage->write();
                    $homePage->publishSingle();
                    $homePage->flushCache();
                    DB::alteration_message(
                        'Existing page ' . $currentTitle . ' of type ' . $currentClass
                        . ' converted to Home Page type',
                        'created'
                    );
                }
            }
            else {
                $homePage = $homeClass::create();
                $homePage->Title = 'Home';
                $homePage->URLSegment = $homeLink;
                $homePage->ParentID = $site->ID;
                $homePage->write();
                $homePage->publishSingle();
                $homePage->flushCache();
                DB::alteration_message('Home page created','created');
            }
        }
    }
}
