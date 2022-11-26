<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFieldsToDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('domains', 'video')) {
            Schema::table('domains', function (Blueprint $table) {
                $table->string('video')->nullable();
            });
        }
        if (!Schema::hasColumn('domains', 'register_text')) {
            Schema::table('domains', function (Blueprint $table) {
                $table->text('register_text')->nullable();
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
        Schema::table('domains', function (Blueprint $table) {
            //
        });
    }
}
