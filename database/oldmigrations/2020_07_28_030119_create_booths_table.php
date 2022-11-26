<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoothsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booths', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('domain_id');
            $table->string('media');
            $table->json('data')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->bigInteger('user_id');
            $table->string('type', 100);
            $table->longText('header_style')->nullable();

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
        Schema::dropIfExists('booths');
    }
}
