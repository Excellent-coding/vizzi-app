<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use App\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ResendPosterEmailsResetPass extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vizzi:send-poster-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send poster reset pass email - PJT HACKED TO SEND EXHIBIT HALL 4 users 9/21 + 9/22 adding tstrome@millbank.org';

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
        $debug = false; // finally works! todo: make me not send
        $skip = false;

        if ($debug) {
            $users = User::where('email', 'jrb436@drexel.edu')->get();
        } else {
// first 4            $users = User::whereIn('email', ['mhicken@umich.edu', 'mpc@umn.edu', 'sarah.dean@unc.edu', 'lweiss@nyam.org'])->get();
//        $users = User::whereIn('email', ['tstrome@millbank.org'])->get();

        $users = User::whereIn('email', ['lweiss@nyam.org'])->get();
// all booth users 9/21            $users = User::whereIn('email', ['tstrome@millbank.org', 'mhicken@umich.edu', 'mpc@umn.edu', 'sarah.dean@unc.edu', 'lweiss@nyam.org'])->get();

        }

        if (count($users) == 0) die("NO POSTER USERS");

        echo count($users) . " users ";
sleep(3); // give pete time to ctrl-c!!!
        foreach ($users as $user) {
            if ($skip &&  $user->id < $skip) continue;
            echo $user->email . "\n";
             Auth::login($user);
             if ($debug) $user->email = 'peter@askuschat.com';
            echo $user->email . "\n";
            try {
                $user->sendPasswordResetNotification('asdf');
            } catch (\Exception $e) {
                echo 'ERROR\n';
                print_r(['user' => $user->id, 'email' => $user->email, $e->getMessage()]);
                echo '\n';
            }
            echo $user->id . "\n";
            if ($debug) break;
        }

    	echo 'sent?';

    }
}
