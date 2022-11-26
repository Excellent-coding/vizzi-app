<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use App\User;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SetUserToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vizzi:set-user-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fun at 4am';

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
        $debug = false;

//    	$users = User::whereIn('email', ['mhicken@umich.edu', 'mpc@umn.edu', 'sarah.dean@unc.edu', 'lweiss@nyam.org'])->get();
   //     $users = User::whereIn('email', ['tstrome@millbank.org'])->get();
        $users = User::whereIn('email', ['lweiss@nyam.org'])->get();
    	echo count($users) . ' users...';
     
        foreach ($users as $user) {
            echo $user->id . "\n";
            if ($debug) break;
            if (!empty($user->token)) continue;
            if (strlen($user->token) > 0) continue;
            $user->setAuthToken();
            $user->save();
            echo $user->id . ":" . $user->token . "\n";
        }

    }
}