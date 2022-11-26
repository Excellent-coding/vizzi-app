<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('headers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('booth_id');
            $table->string('title', 100);
            $table->tinyInteger('status')->default(1);
            $table->longText('content')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->foreign('booth_id')
            ->references('id')->on('booths')
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
        Schema::dropIfExists('headers');
    }
}
