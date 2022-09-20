<?php

namespace Pikselin\YouTube\Extensions;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;

/**
 * Adds the YouTube API key
 * - which tab it's added to is configurable
 */
class YouTubeSiteConfigExtension extends DataExtension
{
    use Configurable;

    private static $db = [
        'YouTubeAPIKey' => 'Varchar',
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldToTab($this->config()->get('add_to_tab'),
            TextField::create(
                'YouTubeAPIKey',
                'YouTube API key'
            )->setDescription("Video duration will not be pulled in and displayed in the video elemental if this isn't set.")
        );
    }
}
