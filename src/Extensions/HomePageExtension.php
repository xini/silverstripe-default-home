<?php

namespace Fromholdio\DefaultHome\Extensions;

use SilverStripe\CMS\Controllers\RootURLController;
use SilverStripe\CMS\Model\SiteTreeExtension;
use SilverStripe\Forms\FieldList;

class HomePageExtension extends SiteTreeExtension
{
    private static $allowed_children = [];

    private static $defaults = [
        'ShowInMenus' => false,
        'Priority' => '1.0',
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName('URLSegment');
    }

    public function updateSettingsFields(FieldList $fields)
    {
        $fields->removeByName('ShowInMenus');
    }

    public function canUnpublish($member = null)
    {
        return false;
    }

    public function canArchive($member = null)
    {
        return false;
    }

    public function onBeforeWrite()
    {
        $this->URLSegment = RootURLController::config()->get('default_homepage_link');
        $this->ShowInMenus = false;
    }
}