<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code');
            $table->string('name');
            $table->string('phone_number');
            $table->string('registration_number')->nullable();
            $table->string('postal_address');

            $table->uuid('administration_level_one_id')->nullable();
            $table->uuid('administration_level_two_id')->nullable();
            $table->uuid('administration_level_three_id')->nullable();
            $table->uuid('country_id')->nullable();
            $table->uuid('relationship_id')->nullable();
            $table->uuid('user_id')->nullable();

            $table->foreign('administration_level_one_id')->references('id')->on('administration_level_ones');
            $table->foreign('administration_level_two_id')->references('id')->on('administration_level_twos');
            $table->foreign('administration_level_three_id')->references('id')->on('administration_level_threes');
            $table->foreign('country_id')->references('id')->on('countries');

            $table->foreign('relationship_id')->references('id')->on('relationships');
            $table->foreign('user_id')->references('id')->on('users');


            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kins');
    }
};
