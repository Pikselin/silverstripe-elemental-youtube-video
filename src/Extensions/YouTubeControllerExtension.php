<?php

namespace Pikselin\YouTube\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

/**
 * Adds css/js requirement directive.
 * NOTE: This js for the video elemental needs jQuery, but we're going to assume the site has JQuery already.
 */
class YouTubeControllerExtension extends Extension
{
    public function onAfterInit()
    {
        Requirements::css('pikselin/silverstripe-elemental-youtube-video:client/css/video.css');
        // deferred so that it loads just before the DOMContentLoaded event.
        Requirements::javascript(
            'pikselin/silverstripe-elemental-youtube-video:client/js/video.js',
            ['defer' => true]
        );
    }
}
