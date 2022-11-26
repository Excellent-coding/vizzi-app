<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegisterSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('register_settings', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('domain_id');

            $table->string('label');
            $table->boolean('required');
            $table->boolean('disabled')->default(false);
            $table->string('order');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('register_settings');
    }
}
