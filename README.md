# Pikselin YouTube elemental

An elemental block with a YouTube video, a block of content and transcripts.

Features:
- video duration is pulled in from YouTube
- admin settings field to enter the YouTube API key
- includes an extension for the controller that does the requirements call for the JS/CSS
- JS for resizing the video and the transcript toggle

## Installation
This module only works with SilverStripe 4.x.

`composer require pikselin/silverstripe-elemental-youtube-video`

Run `dev/build` afterwards.

## Requirements
- These can be found in the composer.json file.
- Assumes your site already has JQuery. If not, this should be added manually.

## Configuration
The YouTube API key field is added to the main tab of the admin settings by default but this configurable. See 
`_config/config.yml`.

## Usage
Make sure to set the YouTube API key in Admin > Settings. The API key will allow the video duration to be pulled in
from YouTube. The video is still functional without it but the duration will not be displayed.

## Templates
You can override the default template by copying `templates/Pikselin/YouTube/Elements/Video.ss` to your own theme.

## CSS
Base styles can be found in:
`client/css/video.css`

## JS
There's built-in JS to handle video resizing and transcript toggling.
`client/js/video.js`

## Notes
This module already activates the CSS and JS files in the PageController
via the `YouTubeControllerExtension.php` extension.
