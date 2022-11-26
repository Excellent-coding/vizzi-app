<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('domain_id');
            $table->string('title');
            $table->string('type');
            $table->string('url');
            $table->string('background')->nullable();
            $table->string('point')->nullable();
            $table->string('order');
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->foreign('domain_id')
            ->references('id')->on('domains')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pages');
    }
}
