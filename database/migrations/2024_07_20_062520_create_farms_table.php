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
        Schema::create('farms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('size');
            $table->enum('ownership',['leased','owned','family_land']);
            $table->enum('terrain',['flat','slope']);
            $table->string('unit_id');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('location');
            $table->uuid('user_id');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('unit_id')->references('id')->on('units');
            $table->foreign('user_id')->references('id')->on('users');
        });



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('farms');
    }
};
