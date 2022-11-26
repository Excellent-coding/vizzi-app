<?php

namespace App\Console\Commands;

use App\Helpers\ZoomAPIHelper;

use DateTime;
use Illuminate\Console\Command;

use Illuminate\Support\Facades\Storage;

class Zoom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vizzi:zoom';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Zoom bootstrap';


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

        $meeting_id = '96345044716';
      
        $request = new \stdClass();
        $request->action = 'start';
        print_r(['response' => $api->doRequest('PATCH', '/meetings/' . $meeting_id . '/livestream/status', [], [], json_encode($request)), 'errors' => $api->requestErrors(), 'status' => $api->responseCode()]);

    }
}