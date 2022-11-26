<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFieldsToVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('videos', function (Blueprint $table) {
            if (Schema::hasColumn('videos', 'category_id')) {
                Schema::table('videos', function (Blueprint $table) {
                    $table->dropColumn('category_id');
                });
            }
            if (Schema::hasColumn('videos', 'track_id')) {
                Schema::table('videos', function (Blueprint $table) {
                    $table->dropColumn('track_id');
                });
            }
            if (Schema::hasColumn('videos', 'page_id')) {
                Schema::table('videos', function (Blueprint $table) {
                    $table->dropColumn('page_id');
                });
            }
            if (Schema::hasColumn('videos', 'start')) {
                Schema::table('videos', function (Blueprint $table) {
                    $table->dropColumn('start');
                    $table->dropColumn('end');
                });
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('videos', function (Blueprint $table) {
            //
        });
    }
}
