<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use App\User;
use App\Models\Track;
use App\Models\Session;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CreateDataFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vizzi:data-from-csv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start of tracks/sessions import command -- will make event day initial import, and customizable for client for excel sptreadsheets to fill in / other integration / api';

    const DOMAIN_ID = 33; // iaphs -- todo: load from option have this as default

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


        // tracks - - Track Order / Number  Track Title Track Description   Event Day Name  Track Hashtag   Track Color                        
    const TRACK_NUM = 0;
    const TRACK_TITLE = 1;
    const TRACK_DESC = 2;
    const TRACK_DAY_CODE = 3; // Day 1 / Day 2 / etc
    const TRACK_HASH = 3;

    const CAT_DAY_TO_ID_IAPHS = [ // these are iaphs - specific event day IDs (event.id)

    ];


    const YES_NO = [
        'Yes' => 1,
        'No' => 0
    ];

    const STREAM_CODE = [
        'pre-recorded' => 0,
        'live' => 1,
        '' => 0,
    ];

    const TRACKS_FILENAME = 'iaphs-tracks-excelexportas.csv';
    const SESSIONS_FILENAME = 'iaphs-sessions-excelexportas.csv';
    const PRESENTER_BIO_FILENAME = 'iaphs-presenterbios-excelexportas.csv';

    const TIMEZONES = [
        'MT' => 'America/Denver', 
        'ET' => 'America/New_York' 
    ];


    const EVENT_DAY_IDS = [
        'Day 1' => 6,
        'Day 2' => 7,
        'Day 3' => 8,
    ];


    const QA_VALUES = [
        'Pending' => 'NQ',
        'Yes' => 'QA',
        'No' => 'NQ',
        'Live' => 'NQ', // yeah, we're all tired 12:47 am 30 mins logged after midnight 1 hour after 11...
        '' => 'NQ',
    ];

    // Session 

    //Order/ Number   Internal ID Session Title   Session Description Session Day Session Track   Session Category    Session Hashtags    Start Time  End Time    Active Button Time  Page Format Pre-recorded or Live    Live Q&A    VIMEO LINK  VIMEO ID    Tag Presenters     
    // 9322 (Brown/Homan),The Toxicity of Structural Discrimination for Population Health: A Focus on Places and Policies,Traditional,Day 1,Panel Discussion 1,,,11:00 AM (ET),12:15 PM (ET),,Live,Pending,,

    const SESSION_DESC = 1;
    const SESSION_TITLE = 1;
    const SESSION_PAGE_FORMAT = 2;
    const SESSION_EVENT_CODE = 3;
    const SESSION_TRACK_CODE = 4;
    const SESSION_PRESENTER_LIST = 5; // json but can we have , sep list in sheet?
    const SESSION_START = 7;
    const SESSION_END = 8;
    const SESSION_IS_LIVE_STREAM_CODE = 10; 
    const SESSION_QA_YES_NO = 11;
    const SESSION_VIMEO_ID = 13;
    const SESSION_TAG = 15;
    const SESSION_PRESENTERS = 16;

    //Email of presenter from Presenter Column in SESSIONS  Bio Plain Text w/ Paragraph Breaks Converted to <p> paragraph tags      
    const BIO_EMAIL = 0;
    const BIO_TEXT = 1;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $debug = true;
        $handle = fopen(storage_path() . '/imports/' . self::TRACKS_FILENAME, 'r');

        

        /*
        CREATE TABLE `tracks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `domain_id` bigint(20) NOT NULL,
  `category_id` bigint(20) NOT NULL,
  `location_id` bigint(20) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `hashtag` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
*/

        $data = fgetcsv($handle, 5000, ","); // skiip/discard header column
        $trackdata = [];

        $data = fgetcsv($handle, 5000, ",");

        while (($data = fgetcsv($handle, 5000, ",")) !== FALSE) {
            $day_title = $data[self::TRACK_DAY_CODE];

            if (empty($day_title)) {
                if ($debug) echo $day_title . "??day?\n";
                continue; // handle error here        
            }

            $day_id = self::EVENT_DAY_IDS[$day_title];

            $trackdata[] = [
                'id'  => $data[self::TRACK_NUM],
                'domain_id' => 33,
                'event_id' => $day_id,
                'category_id' => 0,
                'title' => $data[self::TRACK_TITLE],
                'description' => 0,
                'color' => '#fff',
                'hashtag' => 0,
            ];

//            Track::create();
        }

        if ($debug) echo print_r($trackdata, true);

        foreach ($trackdata as $track) {
            if (!$debug) Track::findOrNew($track);
        }



        $handle = fopen(storage_path() . '/imports/' . self::SESSIONS_FILENAME, 'r');

        $data = fgetcsv($handle, 5000, ","); // skip first line

        /*CREATE TABLE `sessions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `domain_id` bigint(20) NOT NULL,
  `event_id` bigint(20) NOT NULL,
  `category_id` bigint(20) NOT NULL,
  `track_id` bigint(20) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pageType` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hashtag` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `presenter` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button` int(11) DEFAULT NULL,
  `start` time NOT NULL,
  `end` time NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `incoming_stream_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `incoming_stream_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zoom_response` int(11) DEFAULT NULL,
  `stream_active` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stream_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zoom_meeting_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `vimeo_video_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

11:00 AM (ET)
*/

        $sessiondata = [];

        while (($data = fgetcsv($handle, 5000, ",")) !== FALSE) {
            $track_id = 0;
            foreach ($trackdata as $track) {
                $trackObj = (object) $track;
                if ($trackObj->title == $data[self::SESSION_TRACK_CODE]) {
                    $track_id = $trackObj->id;
                    break;
                }
            }
            $day_id = 0;

            foreach ($trackdata as $track) {
                $trackObj = (object) $track;
                if (isset(self::EVENT_DAY_IDS[$data[self::SESSION_EVENT_CODE]]) &&
                    $trackObj->event_id == self::EVENT_DAY_IDS[$data[self::SESSION_EVENT_CODE]]) {
                    $day_id = $trackObj->event_id;
                    break;
                }
            }

            //if ($debug && !isset(self::STREAM_CODE[strtolower($data[self::SESSION_IS_LIVE_STREAM_CODE])])) 

            $start = str_replace(' (ET)', '', $data[self::SESSION_START]);
            $end = str_replace(' (ET)', '', $data[self::SESSION_END]);
            if (strlen($start) > 0) {
                echo $start . "\n";
                    $startam = stripos($start, 'am') !== false;
                    if ($startam) {
                        $start = str_ireplace(' AM', '', $start);
                    } else {
                        $start = str_ireplace(' PM', '', $start);
                        $startparts = explode(':', $start);
                        $hours = intval($startparts[0]);
                        if ($hours < 12) {
                            $hours = $hours + 12;
                            $startparts[0] = $hours;
                            $start = implode(':', $startparts);
                        }
                    }
                    $start = $start . ':00';
                    if (strlen($start) == 7) $start = '0' . $start;
                echo $start . "?\n";
            } else {
                $start = null;
            }


           if (strlen($end) == 0) {
                $end = null;
            } else {
                $end = str_replace(' (ET)', '', $data[self::SESSION_END]);
                $endam = stripos($end, 'am') !== false;
                if ($endam) {
                    $end = str_ireplace(' AM', '', $end);
                } else {
                    $end = str_ireplace(' PM', '', $end);
                    $endparts = explode(':', $end);
                    $hours = intval($endparts[0]);
                    if ($hours < 12) {
                        $hours = $hours + 12;
                        $endparts[0] = $hours;
                        $endparts[0] = $hours;
                        $end = implode(':', $endparts);
                    }
                }
                $end = $end . ':00';
                if (strlen($end) == 7) $end = '0' . $end;
            echo $end . "?\n";
            }


            if ($debug && !isset(self::STREAM_CODE[strtolower($data[self::SESSION_IS_LIVE_STREAM_CODE])])) 
                die($data[self::SESSION_IS_LIVE_STREAM_CODE]);

            $presenters = $data[self::SESSION_PRESENTER_LIST];
            if (is_array($presenters)) {

            } else if (self::goodString($presenters)) {
                $presenters = explode(',', $presenters);
            } else {
                $presenters = [];
            }
            
            $sessiondata[] = [
                'domain_id' => self::DOMAIN_ID,
                'event_id' => $day_id,
                'track_id' => $track_id,
                'category_id' => 0,
                'description'  => $data[self::SESSION_DESC] . ":" . self::QA_VALUES[$data[self::SESSION_QA_YES_NO]],
                'pageType' => $data[self::SESSION_PAGE_FORMAT],
                'title' => $data[self::SESSION_TITLE],
                'start' => $start,
                'end' => $end,
                'stream_active' => 0, //self::STREAM_CODE[strtolower($data[self::SESSION_IS_LIVE_STREAM_CODE])],
                'vimeo_video_id' => $data[self::SESSION_VIMEO_ID],
                'presenter' => json_encode($presenters),
            ];
        }

        if ($debug) echo print_r($sessiondata, true); 

        foreach ($sessiondata as $session) {
            if (!$debug) Session::findOrNew($session);
        }


        $presenterdata = [];
        $presenteruserObjs = []; 

        foreach ($sessiondata as $session) {
            $sessionObj = (object) $session;
            print_r($sessionObj);
            $presentersData = json_decode($session['presenter']);
            if (!is_array($presentersData)) { // error handling! important
                if ($debug) die("ERROR: " . print_r($presentersData, true)); // after checking can just skip
                echo "presenters json missing?\n";
                if (empty($session->presenter) || 
                    strlen($session->presenter) == 0) {
                    continue;
                }
            } else {
                echo "???";
            }
            // todo: if live? if (empty($session->zoom_meeting_id)) {
            for ($i=0; $i<count($presentersData); $i++) {
                $email = $presentersData[$i];
                
                // sloppy but works
                $handlebio = fopen(storage_path() . '/imports/' . self::PRESENTER_BIO_FILENAME, 'r');

                while (($databios = fgetcsv($handlebio, 5000, ",")) !== FALSE) {
                    if (self::goodString(trim($databios[self::BIO_EMAIL]))) {
                        $emailCleaned = trim(strtolower($email));
                        if (self::goodString($emailCleaned)) {
                            if ($emailCleaned == $databios[self::BIO_EMAIL]) {
                                $registerMeta = new \stdClass();
                                $registerMeta->bio = $databios[self::BIO_TEXT];

                                $userData = [
                                    'name' => '',
                                    'email' => $emailCleaned,
                                    'password' => bcrypt($emailCleaned . time()),
                                    'domain_id' => self::DOMAIN_ID,
                                    'register_metadata' => json_encode($registerMeta)
                                ];

                                if ($debug) print_r($userData);
                                else {
                                  $user = User::findOrNew([
                                    'name' => '',
                                    'email' => $emailCleaned,
                                    'password' => bcrypt($emailCleaned . time()),
                                    'domain_id' => self::DOMAIN_ID,
                                    'register_metadata' => json_encode($registerMeta)
                                  ]);
                                }
                            }
                        }
                    } 
                }
                //todo: Create presenter record only in zoomevent
            }
        }


    }

    public static function goodString($s) {
        if (!empty($s) && 
            strlen($s) > 0) {
            return true;
        }

        return false;
    }
}