<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecreateVideoPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('video_pages');
        Schema::create('video_pages', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('domain_id');
            $table->bigInteger('category_id');
            $table->bigInteger('track_id');
            $table->string('title');
            $table->date('date');
            $table->time('start');
            $table->time('end');
            $table->string('format');
            $table->string('kind');
            $table->string('link')->nullable();
            $table->tinyInteger('isChat')->default(1);
            $table->tinyInteger('isNote')->default(1);
            $table->string('description')->nullable();
            $table->string('hashtag')->nullable();
            $table->string('presenter')->nullable();
            $table->string('readMore')->nullable();
            $table->string('code')->nullable();

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
        Schema::table('video_pages', function (Blueprint $table) {
            //
        });
    }
}
