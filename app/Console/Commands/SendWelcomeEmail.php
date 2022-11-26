<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Notifications\Messages\MailMessage;

use App\User;

class SendWelcomeEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:welcome-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send welcome email to frontend user. Email contains link to registration page.';

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
     * @return int
     */
    public function handle()
    {

        $users = User::where([
			'role' => '5',
		])
		->where('id', '>', '210667')
		->get();

		if(!$users) {
			exit('no users found, not sending emails');
		} 

		try {
			foreach ($users as $user){
				echo $user->id . PHP_EOL;
				$user->sendWelcomeEmail();
            }
		} catch (\Exception $error) {
			var_dump($error);
			exit('Failed to send welcome message!');
		}

    }
}
