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
        Schema::create('loans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->double('sub_total')->nullable();
            $table->double('total')->nullable();
            $table->string('code');
            $table->uuid('user_id');
            $table->uuid('wallet_transaction_id')->nullable();
            $table->uuid('status_id');
            $table->double('interest')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('wallet_transaction_id')->references('id')->on('wallet_transactions');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('status_id')->references('id')->on('statuses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('loans');
    }
};
