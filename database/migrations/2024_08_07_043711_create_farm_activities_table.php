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
        Schema::create('farm_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('cohort_id');
            $table->uuid('user_id');
            $table->uuid('farm_id');
            $table->uuid('loan_package_id');
            $table->uuid('wallet_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('cohort_id')->references('id')->on('cohorts');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('farm_id')->references('id')->on('farms');
            $table->foreign('wallet_id')->references('id')->on('wallets');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('farm_activities');
    }
};
