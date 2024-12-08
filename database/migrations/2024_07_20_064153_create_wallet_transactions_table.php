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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->double('amount');
            $table->string('code');
            $table->uuid('wallet_id');
            $table->enum('type', ['credit','debit']);
            $table->string('user_id');
            $table->timestamps();

            $table->foreign('wallet_id')->references('id')->on('wallets');
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
        Schema::drop('wallet_transactions');
    }
};
