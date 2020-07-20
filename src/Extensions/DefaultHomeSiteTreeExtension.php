<?php

namespace Innoweb\DefaultHome\Extensions;

use SilverStripe\CMS\Controllers\RootURLController;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\CMS\Model\SiteTreeExtension;
use SilverStripe\ORM\DB;

class DefaultHomeSiteTreeExtension extends SiteTreeExtension
{
    public function requireDefaultRecords()
    {
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
                . 'and that var must be the name of a valid SiteTree subclass. Supplied value "' . $class
                . '" is not a valid subclass of SiteTree.'
            );
        }

        $homeLink = RootURLController::get_homepage_link();
        $homePage = SiteTree::get_by_link($homeLink);
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
            $homePage->ParentID = 0;
            $homePage->write();
            $homePage->publishSingle();
            $homePage->flushCache();
            DB::alteration_message('Home page created','created');
        }
    }
}
