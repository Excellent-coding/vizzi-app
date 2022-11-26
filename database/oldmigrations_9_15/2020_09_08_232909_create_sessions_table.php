<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('sessions');
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('domain_id');
            $table->bigInteger('event_id');
            $table->bigInteger('category_id');
            $table->bigInteger('track_id');
            $table->string('title');
            $table->string('pageType')->nullable();
            $table->string('description')->nullable();
            $table->string('hashtag')->nullable();
            $table->string('presenter')->nullable();
            $table->string('url')->nullable();
            $table->integer('button')->nullable();
            $table->time('start');
            $table->time('end');

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
        Schema::dropIfExists('sessions');
    }
}
