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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone_number');
            $table->string('username')->unique();
            $table->string('registration_number');
            $table->uuid('status_id');
            $table->uuid('role_id');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            $table->uuid('administration_level_one_id')->nullable();
            $table->uuid('administration_level_two_id')->nullable();
            $table->uuid('administration_level_three_id')->nullable();
            $table->uuid('country_id')->nullable();

            $table->foreign('administration_level_one_id')->references('id')->on('administration_level_ones');
            $table->foreign('administration_level_two_id')->references('id')->on('administration_level_twos');
            $table->foreign('administration_level_three_id')->references('id')->on('administration_level_threes');
            $table->foreign('country_id')->references('id')->on('countries');

            $table->foreign('status_id')->references('id')->on('statuses');
            $table->foreign('role_id')->references('id')->on('roles');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
