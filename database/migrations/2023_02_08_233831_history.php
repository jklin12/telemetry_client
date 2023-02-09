<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sch_station_history', function (Blueprint $table) {
            $table->id('history_id');
            $table->string('station');
            $table->string('assets')->nullable();
            $table->string('history_title');
            $table->text('history_body')->nullable();
            $table->string('history_tumbnial')->nullable();
            $table->string('history_imgae')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        //
    }
};
