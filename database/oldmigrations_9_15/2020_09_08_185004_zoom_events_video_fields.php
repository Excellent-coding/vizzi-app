<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ZoomEventsVideoFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('domains', 'zoom_system_user_id')) {
            Schema::table('domains', function (Blueprint $table) {
                $table->string('zoom_system_user_id')->after('status')->nullable();
                $table->string('zoom_response')->after('status')->nullable();
                $table->string('zoom_passcode')->after('status')->nullable();
            });
            Schema::table('sessions', function (Blueprint $table) {
                $table->string('status')->after('end')->nullable();
                $table->string('zoom_meeting_id')->after('status')->nullable();
                $table->string('stream_url')->after('status')->nullable();
                $table->string('stream_active')->after('status')->nullable();
                $table->string('zoom_response')->after('status')->nullable();
            });
        }
        
        if (!Schema::hasTable('session_presenters')) {
            Schema::create('session_presenters', function (Blueprint $table) {
                $table->id();

                $table->bigInteger('domain_id');
                $table->bigInteger('user_id');
                $table->integer('type');
                $table->string('name');
                $table->string('zoom_user_id');
                $table->string('zoom_username');
                $table->string('zoom_password');
                $table->string('zoom_response');
                $table->tinyInteger('status')->default(1);

                $table->timestamps();
            });
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
