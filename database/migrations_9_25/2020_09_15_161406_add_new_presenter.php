<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewPresenter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

            $presenter_names = ['Ned Rauch-Mannino'];

            for ($i=0; $i<count($presenter_names); $i++) {
                $email = strtolower(str_ireplace(' ', '_', $presenter_names[$i])) . '@mbda.presenter';
                $user = App\User::create([
                    'name' => $presenter_names[$i],
                    'email' => $email,
                    'password' => bcrypt($email),
                    'domain_id' => 31,
                    'register_metadata' => ''
                ]);

                $user->type = 10;
                $user->save();
            }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
