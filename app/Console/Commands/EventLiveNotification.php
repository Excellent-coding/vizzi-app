<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\PubnubConfig;
use App\Models\Session;
use App\User;
use Illuminate\Notifications\Messages\MailMessage;

class EventLiveNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vizzi:sessionlive-pubhub';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Put messages to pubnub';

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
        $sessionItems = Session::with('event:id,date')->with('domain:id,domain')->get();
        $messages = array();
        $domains = array();
    
        foreach ($sessionItems as $event) {
            if ($event['event'] != null){
                try {
                    $domain_id = $event['domain_id'];
                    $date = $event['event']['date'];
                    $start = $event['start'];
                    $end = $event['end'];
                    $title = $event['title'];
                    //$timezone = $event['timezone'];
                    $domain_name = $event['domain']['domain'];
    
                    
                    
                    if (($start == '') || ($end == '')) {
                        continue;
                    }
    
                    if ((strlen($start) != 8) || (strlen($end) != 8)) {
                        continue;
                    }
    
                    //Checking remaning hours from now to event start date.        
                    $eventDate = \DateTime::createFromFormat('Y-m-d H:i:s', $date.' '.$start);
                    $current_date = new \DateTime();
                    $diff = $eventDate->diff($current_date);
    
                    $diffHours = $diff->d * 24 + $diff->h;
                    
                    if ($diffHours == 1) {
                        $notification = $title.' is begining here shortly at '.$eventDate->format('Y-m-d H:i:s');
                        
                        if (array_key_exists($domain_name, $messages)){
                            
                        } else {
                            $messages = array($domain_name => []);
                        }
    
                        array_push($messages[$domain_name], $notification);
                    }

                    array_push($domains, $domain_name);

                } catch (\GuzzleHttp\Exception\ClientException $e) {
                    echo json_encode($event);
                }
            }
        }
        
        foreach($domains as $domain) {
            $pubnub = new PubnubConfig($domain);
            $pubnub->deleteAllMessages($domain);
        }

        foreach($messages as $key=>$notifications) {
            $pubnub = new PubnubConfig($key);
    
            foreach($notifications as $notification) {
                $pubnub->publishMessage($notification, $domain_name);
            }
        }
    }
}