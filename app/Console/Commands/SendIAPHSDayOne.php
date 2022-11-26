<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\User;

class SendIAPHSDayOne extends Command {

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'send:iaphs-day-one';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send day one email for IAPHS';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle() {
		$users = User::where('id', '>', '0')
			->get()
			->toArray();

		if ( ! $users ) {
			exit( 'no users found, not sending emails' );
		}

		$offset = 0;
		$length = 50;

		try {
			for(
				$offset = 0;
				$offset < (count($users) / $length);
				$offset++
			) {
				$user_batch_set = array_slice(
					$users,
					$offset === 0 ? $length : $length * $offset,
					$length
				);

				echo "User Batch Index: " . $offset . PHP_EOL;
				echo "Total: " . count($user_batch_set) . PHP_EOL;
				
				foreach($user_batch_set as $u) {
					$user = User::where('id', $u['id'])->first();
					echo "Sending to user id: " . $user->id . PHP_EOL;
					$user->sendIAPHSDayOne();
					sleep(1);
				}
			}
		} catch ( \Exception $error ) {
			var_dump( $error->getMessage() );
			exit( 'Failed to send welcome message!' );
		}
	}
}
