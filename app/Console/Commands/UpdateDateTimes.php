<?php

namespace App\Console\Commands;

use App\Helpers\ZoomAPIHelper;

use DateTime;
use Illuminate\Console\Command;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Models\VideoPage;
use App\Models\Video;

use App\Models\TrackCategory;
use App\Models\Track;
use App\Models\Page;
use App\Models\Session;
use App\Models\Domain;

class UpdateDateTimes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vizzi:update-datetimes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert timesestored in database as eastern time to UTC';


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
        $user = User::find(145)->first();
         Auth::login($user);

        $videos = VideoPage::all();

        foreach ($videos as $videoPage) {
            $startdate = DateTime::createFromFormat('Y/m/d H:i', $videoPage->date . ' ' . $videoPage->start);
            echo "EASTERN: " . $startdate->format('H:i') . "\n";
            $enddate = DateTime::createFromFormat('Y/m/d H:i', $videoPage->date . ' ' . $videoPage->end);

            $startdate = $user->userTimeToDB($startdate);
            $enddate = $user->userTimeToDB($enddate);
            echo "UTC: " . $startdate->format('H:i') . "\n";
            die();
            try {
                $videoPage->update([
                    'date'   =>  $startdate->format('Y/m/d'),
                    'start'   =>  $startdate->format('H:i'),
                    'end'   =>  $enddate->format('H:i')
                ]);
            } catch(\Exception $e) {
                print_r($e);
            }
        }
    }
}