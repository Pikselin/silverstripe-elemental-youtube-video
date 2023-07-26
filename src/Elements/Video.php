<?php

namespace Pikselin\YouTube\Elements;

use DateInterval;
use DNADesign\Elemental\Models\BaseElement;
use GuzzleHttp\Client;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\TextField;
use SilverStripe\SiteConfig\SiteConfig;

class Video extends BaseElement
{
    private static $singular_name = 'Video';
    private static $plural_name = 'Video';
    private static $description = 'Video';
    private static $icon = 'font-icon-fast-forward';
    private static $table_name = 'ElementVideo';

    private static $db = [
        'VideoID' => 'Varchar(255)',
        'Transcription' => 'HTMLText',
        'YouTubeData' => 'Text',
        'Content' => 'HTMLText',
    ];  

    public function getSummary()
    {
        $summary = parent::getSummary();
        if (!empty($this->VideoID)) {
            return $summary . $this->VideoID;
        }
        return $summary . 'No video ID';
    }

    /**
     * Grab the YouTube API key from SiteConifg.
     * If this is not specified, the duration won't be displayed as this is pulled from YouTube.
     *
     * @return string
     */
    private static function YouTubeAPIKey()
    {
        $SiteConfig = SiteConfig::current_site_config();
        if ($SiteConfig->YouTubeAPIKey) {
            return trim($SiteConfig->YouTubeAPIKey);
        }

        return false;
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $title = $fields->fieldByName('Root.Main.Title');
        $title->setDescription('Required for accessibility purposes. If set to display, this will appear as an H2 above the video and any text.');

        $fields->addFieldToTab('Root.Main',
            TextField::create('VideoID', 'Video URL')
                ->setDescription('Enter the video URL.<br/>e.g. https://www.youtube.com/watch?v=87mvQuVKeuU')
        );

        $fields->addFieldToTab('Root.Main',
            HTMLEditorField::create('Content',
                'Text content that sits beside video (or below, in mobile)')
                ->setRows(5)
                ->setDescription('Note - if you\'ve used chosen to display the top title field, the best heading to use here (if you want to use one) is an &lt;h3&gt;')
        );

        $fields->addFieldToTab('Root.Main',
            HTMLEditorField::create('Transcription', 'Video transcription text')
                ->setRows(15)
        );

        //$fields->addFieldToTab('Root.Main', HiddenField::create('YouTubeData'));
        $fields->addFieldToTab('Root.Main',
            HiddenField::create('YouTubeData'));

        // if you need to debug the JSON being populated by the onBeforeWrite function...
        //$fields->addFieldToTab('Root.Main', TextareaField::create('YouTubeData'));

        return $fields;
    }

    public function getCMSValidator()
    {
        return new RequiredFields([
            'Title',
            'VideoID',
            'Content',
        ]);
    }

    /**
     * Parses a YouTube video link and pulls out the video ID
     *
     * see:
     * http://stackoverflow.com/questions/2936467/parse-youtube-video-id-using-preg-match
     *
     * @param $url
     *
     * @return bool|string
     */
    public function getYouTubeID($url = FALSE)
    {
        if(!$this->VideoID) {
            return false;
        }
        
        if (!$url) {
            $url = $this->VideoID;
        }

        // some of our older entries just use the ID, so check for that...
        if (!empty($url) && strlen(trim($url)) === 11) {
            return trim($url);
        }

        // the magic
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i',
            $url, $match);
        return isset($match[1]) ? $match[1] : FALSE;
    }

    /**
     * Grabs video data from YouTube API that we can use in the front-end
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        $videoID = $this->getYouTubeID($this->VideoID);

        // don't bother if there's no ID
        if (empty($videoID) || !$this->YouTubeAPIKey()) {
            return;
        }

        // use Guzzle
        $client = new Client();
        $arrURL = [
            'https://www.googleapis.com/youtube/v3/videos?part=contentDetails,snippet&id=',
            $videoID,
            '&key=',
            $this->YouTubeAPIKey(),
        ];
        $url = implode('', $arrURL);
        $response = $client->get($url);

        if ($response) {
            $body = json_decode($response->getBody());
            if (isset($body->items[0])) {
                $this->setField('YouTubeData', json_encode($body->items[0]));
            }
            else {
                $this->setField('YouTubeData', 'no video data found');
            }
        }
        else {
            $this->setField('YouTubeData', 'no response?');
        }
    }

    public function getType()
    {
        return 'Video';
    }

    /**
     * Provides a summary to the gridfield.
     *
     * @return array
     * @throws \SilverStripe\ORM\ValidationException
     */
    protected function provideBlockSchema()
    {
        $blockSchema = parent::provideBlockSchema();
        $blockSchema['content'] = $this->getSummary();
        return $blockSchema;
    }

    // grabs the duration of the video out of our stored YouTube data
    public function getDuration()
    {
        // sanity check
        if (empty($this->YouTubeData)) {
            return FALSE;
        }

        // decode our stored JSON and sanity check that...
        $data = json_decode($this->YouTubeData);
        if (isset($data->contentDetails->duration)) {
            return $this->formatDuration($data->contentDetails->duration);
        }

        // nothing?
        return FALSE;
    }

    /**
     * Converts the ISO 8061 format to hh:mm:ss format
     *
     * @param $strDuration
     *
     * @return string
     */
    public function formatDuration($strDuration)
    {
        $objDuration = new DateInterval($strDuration);

        // default format is just minutes:seconds
        $format = '%I:%S';

        // if it's longer than a day...
        if ($objDuration->d > 0) {
            $format = '%d days %H:%I:%S';
        }
        // ...or longer than an hour
        elseif ($objDuration->h > 0) {
            $format = '%H:%I:%S';
        }

        // format and send it back
        $strNiceDuration = $objDuration->format($format);
        return $strNiceDuration;
    }
}
