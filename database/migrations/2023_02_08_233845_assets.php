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
        Schema::create('sch_station_assets', function (Blueprint $table) {
            $table->id('assets_id');
            $table->string('station');
            $table->string('asset_name');
            $table->string('asset_brand')->nullable();
            $table->string('asset_type')->nullable();
            $table->string('asset_serial_number')->nullable();
            $table->text('asset_spesification')->nullable();
            $table->string('asset_year')->nullable();
            $table->string('asset_tumbnial')->nullable();
            $table->string('asset_imgae')->nullable();
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
