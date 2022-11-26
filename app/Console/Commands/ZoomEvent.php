<?php

namespace App\Console\Commands;

use App\Helpers\ZoomAPIHelper;
use App\Models\Domain;
use App\Models\Event;
use App\Models\Session;
use App\User;
use App\Models\Video;

use DateTime;
use Illuminate\Console\Command;

use Illuminate\Support\Facades\Storage;

class ZoomEvent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vizzi:zoom-event {domain=31}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Zoom bootstrap event for domain - pass domain ID';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $api = ZoomAPIHelper::create();

        $domain = Domain::find($this->argument('domain'));
        $user = $domain->admin()->first();
        $nameparts = explode(' ', $user->name);

        if (empty($domain->zoom_system_user_id)) {
            // check if user already on zoom account
            $response = (object) $api->doRequest('GET', '/users/{userId}', [], ['userId' => $user->email]);

            print_r(['response' => $response, 'errors' => $api->requestErrors(), 'status' => $api->responseCode()]);  

            if (isset($response->id)) {
                $domain->zoom_system_user_id = $response->id;
                $domain->save();
            }
        }

        if (empty($domain->zoom_system_user_id)) {
            $request = new \stdClass();
            $request->action = 'custCreate';
            $request->user_info = new \stdClass();
            $request->user_info->email = $user->email;
            $request->user_info->type = ZoomAPIHelper::USER_TYPE_LICENSED;
            $request->user_info->first_name = $nameparts[0];
            $request->user_info->last_name = $nameparts[count($nameparts)-1];
            //die(json_encode($request));
            $response = (object) $api->doRequest('POST', '/users', [], [], json_encode($request));

            $details = ['request' => $request, 
                'action' => 'POST /users', 
                'response' => $response, 
                'errors' => $api->requestErrors(), 
                'status' => $api->responseCode()];

            print_r($details);

            if (isset($response->id)) {
                $domain->zoom_system_user_id = $response->id;
                $domain->zoom_response = json_encode($response);
                $domain->save();
            } else {
                die('error');
            }

        } else {
            //echo "??" . $domain->zoom_system_user_id . "??\n";
            //print_r(['response' => $api->doRequest('GET', '/users/' . $domain->zoom_system_user_id), 'errors' => $api->requestErrors(), 'status' => $api->responseCode()]);

        }

        $sessions = Session::where([
            'domain_id' => $domain->id,
            'type' => 'Live'
            
        ])->get();

        echo count($sessions) . " sessions...\n";
        foreach ($sessions as $session) {
            //             $presenters = json_decode($session->presenter);
            echo "SESSION " . $session->id . "\n";
            $presenters = json_decode($session->presenter);

            $request = new \stdClass();
            $request->topic = $session->title;
            $request->type = ZoomAPIHelper::MEETING_TYPE_INSTANT;
            $request->password = 'ZEwfWjZJxY';
            $request->settings = new \stdClass();
            $request->settings->host_video = true;
            $request->settings->watermark = true;
            $request->settings->approval_type = ZoomAPIHelper::MEETING_APPROVAL_TYPE_NONE;
            $request->settings->auto_recording = 'cloud';
            $request->settings->alternative_hosts = implode(",", $presenters);
            if (empty($session->zoom_meeting_id)) {
                for ($i=0; $i<count($presenters); $i++) {
                    $email = $presenters[$i];

                    $response = (object) $api->doRequest('GET', '/users/{userId}', [], ['userId' => $email]);

                    //print_r(['response' => $response, 'errors' => $api->requestErrors(), 'status' => $api->responseCode()]);  

                    if (isset($response->id)) {
                        //$domain->zoom_system_user_id = $response->id;
                        //$domain->save();
                    } else {
                        $request = new \stdClass();
                        $request->action = 'custCreate';
                        $request->user_info = new \stdClass();
                        $request->user_info->email = $email;
                        $request->user_info->type = ZoomAPIHelper::USER_TYPE_LICENSED;
                        $request->user_info->first_name = $session->title . " Presenter " . ($i+1);
    //                    $request->user_info->last_name = $nameparts[count($nameparts)-1];
                        //die(json_encode($request));
                        $response = (object) $api->doRequest('POST', '/users', [], [], json_encode($request));

                        $details = ['request' => $request, 
                            'action' => 'POST /users', 
                            'response' => $response, 
                            'errors' => $api->requestErrors(), 
                            'status' => $api->responseCode()];

                        //print_r($details);

                        if (isset($response->id)) {
                         //   $domain->zoom_system_user_id = $response->id;
                        }                    
                    }
                    /*
                    $request = new \stdClass();
                    $request->type = ZoomAPIHelper::USER_TYPE_LICENSED;
//                    $request->user_info->last_name = $nameparts[count($nameparts)-1];
                    //die(json_encode($request));
                    $response = (object) $api->doRequest('PATCH', '/users/{userId}', [], [
                        'userId' => $email
                    ], json_encode($request));

                    $details = ['request' => $request, 
                        'action' => 'PATCH /users', 
                        'response' => $response, 
                        'errors' => $api->requestErrors(), 
                        'status' => $api->responseCode()];

                    print_r($details);
                    */
                }
                // alternative_hosts: Alternative host’s emails or IDs: multiple values separated by a comma. 

                $response = (object) $api->doRequest('POST', '/users/' . $domain->zoom_system_user_id . '/meetings', [], [], json_encode($request));
                print_r(['response' => $response, 'errors' => $api->requestErrors(), 'status' => $api->responseCode()]);
                if (isset($response->id)) {
                    $meeting_id = $response->id;
                } else {
                    die("Could not create meeting" . print_r($api->requestErrors(), true));
                }

                $session->zoom_meeting_id = $meeting_id;
                $session->zoom_response = json_encode($response);
                $session->incoming_stream_url = 'rtmp://' . config('services.zoom.wowza_video_1') . ':1935/' . config('services.zoom.wowza_application_name') . '';
                $session->incoming_stream_name = config('services.zoom.wowza_stream_name');
                $session->save();
            } else {
                $meeting_id = $session->zoom_meeting_id;
                $response = (object) $api->doRequest('GET', '/meetings/' . $meeting_id);
                print_r(['response' => $response, 'errors' => $api->requestErrors(), 'status' => $api->responseCode()]);
                if (intval($api->responseCode()) == 404) {
                    echo "Meeting not found - recreating...\n";
                    $response = (object) $api->doRequest('POST', '/users/' . $domain->zoom_system_user_id . '/meetings', [], [], json_encode($request));
                    print_r(['response' => $response, 'errors' => $api->requestErrors(), 'status' => $api->responseCode()]);
                    if (isset($response->id)) {
                        $meeting_id = $response->id;
                    } else {
                        die("Could not create meeting" . print_r($api->requestErrors(), true));
                    }
                    $session->zoom_meeting_id = $meeting_id;
                    $session->zoom_response = json_encode($response);
                    $session->incoming_stream_url = 'rtmp://' . config('services.zoom.wowza_video_1') . ':1935/' . config('services.zoom.wowza_application_name') . '';
                    $session->incoming_stream_name = config('services.zoom.wowza_stream_name');
                    $session->save();
                }
            }
//          $meeting_id = '99050305674';

            $sessionVideos = Video::where([
                'domain_id' => $domain->id,
                'session_id' => $session->id
            ])->get();

            if (count($sessionVideos) == 0) {
                $video = Video::create([
                    'domain_id'     =>  $domain->id,
                    'session_id'    =>  $session->id,
                    'kind'          =>  'live',
                    'format'        => 'application/dash+xml',
                    'title'         =>  $session->title,
                    //'link'           =>  $link,
                    //'description'   =>  $description,
                    //'hashtag'       =>  $hashtag,
                    'presenter'     =>  $user->name,
                    'isChat'        =>  1,
                    'isNote'        =>  1,
                    'status' => 1, // todo: make constants
                    'stream_url' => '' . config('services.zoom.wowza_video_1') . ':1935/' . config('services.zoom.wowza_application_name') . '/' . config('services.zoom.wowza_stream_name') . '/manifest.mpd',
                    'stream_name' => config('services.zoom.wowza_stream_name'), // this will change per session
                    'stream_active' => 0
                ]);
                $video = Video::create([
                    'domain_id'     =>  $domain->id,
                    'session_id'    =>  $session->id,
                    'kind'          =>  'live',
                    'format'        => 'application/vnd.apple.mpegurl',
                    'title'         =>  $session->title,
                    //'link'           =>  $link,
                    //'description'   =>  $description,
                    //'hashtag'       =>  $hashtag,
                    'presenter'     =>  $user->name,
                    'isChat'        =>  1,
                    'isNote'        =>  1,
                    'status' => 1, // todo: make constants
                    'stream_url' => '' . config('services.zoom.wowza_video_1') . ':1935/' . config('services.zoom.wowza_application_name') . '/' . config('services.zoom.wowza_stream_name') . '/playlist.m3u8',
                    'stream_name' => config('services.zoom.wowza_stream_name'), // this will change per session
                    'stream_active' => 0
                ]);
            } else {
                $video = $sessionVideos[0];
                if (count($sessionVideos) == 1) {                
                    $video = Video::create([
                        'domain_id'     =>  $domain->id,
                        'session_id'    =>  $session->id,
                        'kind'          =>  'live',
                        'format'        => 'application/vnd.apple.mpegurl',
                        'title'         =>  $session->title,
                        //'link'           =>  $link,
                        //'description'   =>  $description,
                        //'hashtag'       =>  $hashtag,
                        'presenter'     =>  $user->name,
                        'isChat'        =>  1,
                        'isNote'        =>  1,
                        'status' => 1, // todo: make constants
                        'stream_url' => '' . config('services.zoom.wowza_video_1') . ':1935/' . config('services.zoom.wowza_application_name') . '/' . config('services.zoom.wowza_stream_name') . '/playlist.m3u8',
                        'stream_name' => config('services.zoom.wowza_stream_name'), // this will change per session
                        'stream_active' => 0
                    ]);
                }
            }

            $request = new \stdClass();
            $request->stream_url = $session->incoming_stream_url;
            $request->stream_key = $session->incoming_stream_name;

            $response = (object) $api->doRequest('PATCH', '/meetings/' . $meeting_id . '/livestream', [], [], json_encode($request));

            print_r(['response' => $response, 'errors' => $api->requestErrors(), 'status' => $api->responseCode()]);

            $video->zoom_response = json_encode($response);
            $video->save();
        }
    }
}