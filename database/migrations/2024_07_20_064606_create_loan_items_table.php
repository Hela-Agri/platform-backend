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
        Schema::create('loan_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('item_id');
            $table->double('amount');
            $table->string('code');
            $table->uuid('loan_id');
            $table->uuid('status_id');
            $table->uuid('farm_activity_item_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('status_id')->references('id')->on('statuses');
            $table->foreign('loan_id')->references('id')->on('loans');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('loan_items');
    }
};
