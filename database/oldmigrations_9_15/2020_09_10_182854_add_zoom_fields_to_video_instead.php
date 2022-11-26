<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddZoomFieldsToVideoInstead extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('video_instead', function (Blueprint $table) {
            Schema::table('videos', function (Blueprint $table) {
                $table->string('status')->nullable();
                $table->string('stream_url')->after('status')->nullable();
                $table->string('stream_name')->after('status')->nullable();
                $table->string('stream_active')->after('stream_url')->nullable();
                $table->string('zoom_response')->after('stream_active')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('video_instead', function (Blueprint $table) {
            //
        });
    }
}
