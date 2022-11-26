<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use App\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SentTestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vizzi:send-test-email {email=peter@askuschat.com}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send test email';

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
        $user = User::where('email', $this->argument('email'))->first();
        if (empty($user)) die("USER NOT FOUND" . $this->argument('email'));
         Auth::login($user);

    	$user->sendPasswordResetNotification('asdf');

    	echo 'sent?';

    }
}