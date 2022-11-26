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

class ZoomConnect extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vizzi:zoom-connect {session=5}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Connects zoom presenter stream to wowza and other vizzi services';


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

     //   print_r(['response' => $api->doRequest('GET', '/users/RlIGj6MsSsS5s5OSLqHHpQ'), 'errors' => $api->requestErrors(), 'status' => $api->responseCode()]);

       // print_r(['response' => $api->doRequest('GET', '/users'), 'errors' => $api->requestErrors(), 'status' => $api->responseCode()]);
/*
        $request = new \stdClass();
        $request->action = 'create';
        $request->user_info = new \stdClass();
        $request->user_info->email = 'peter@askuschat.com';
        $request->user_info->type = ZoomAPIHelper::USER_TYPE_BASIC;
        $request->user_info->first_name = "Peter";
        $request->user_info->last_name = "Tracey";
//        die(json_encode($request));
        print_r(['response' => $api->doRequest('POST', '/users', [], [], json_encode($request)), 'errors' => $api->requestErrors(), 'status' => $api->responseCode()]);

        $request = new \stdClass();
        $request->topic = 'test 1';
        $request->type = ZoomAPIHelper::MEETING_TYPE_INSTANT;
        $request->password = 'ZEwfWjZJxY';
        $request->settings = new \stdClass();
        $request->settings->host_video = true;
        // alternative_hosts: Alternative host’s emails or IDs: multiple values separated by a comma. 

        $response = $api->doRequest('POST', '/users/RlIGj6MsSsS5s5OSLqHHpQ/meetings', [], [], json_encode($request));
        print_r(['response' => $response, 'errors' => $api->requestErrors(), 'status' => $api->responseCode()]);

        $meeting_id = $response['id'];
  */
///https://api.zoom.us/v2/meetings/97245557337/livestream

        $api = ZoomAPIHelper::create();

        $session = Session::find($this->argument('session')); //where('domain_id', $domain->id)->get();

        $domain = Domain::find($session->domain_id);
        $user = $domain->admin()->first();
        $nameparts = explode(' ', $user->name);

        //$response = $api->doRequest('GET', '/users/' . $domain->zoom_system_user_id . '/meetings');
        //print_r(['response' => $response, 'errors' => $api->requestErrors(), 'status' => $api->responseCode()]);

        $sessionVideos = Video::where([
            'domain_id' => $domain->id,
            'session_id' => $session->id
        ])->get();

        foreach ($sessionVideos as $video) {

            $meeting_id = $session->zoom_meeting_id;
          
            echo $session->stream_url . "\n";

            $request = new \stdClass();
            $request->stream_url = $session->incoming_stream_url;
            $request->stream_key = $session->incoming_stream_name;

            $response = (object) $api->doRequest('PATCH', '/meetings/' . $meeting_id . '/livestream', [], [], json_encode($request));

            print_r(['response' => $response, 'errors' => $api->requestErrors(), 'status' => $api->responseCode()]);

            $request = new \stdClass();
            $request->action = 'start';

            $response = (object) $api->doRequest('PATCH', '/meetings/' . $meeting_id . '/livestream/status', [], [], json_encode($request));

            print_r(['response' => $response, 'errors' => $api->requestErrors(), 'status' => $api->responseCode()]);

            if (false) {        
                $video->stream_active = 1;
                $video->zoom_response = json_encode($response);
                $video->save();
            }
        }
    }
}