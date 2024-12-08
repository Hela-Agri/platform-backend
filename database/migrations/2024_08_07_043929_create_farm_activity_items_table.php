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
        Schema::create('farm_activity_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('farm_activity_id');
            $table->uuid('rate_card_id');
            $table->decimal('quantity');
            $table->decimal('total');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('farm_activity_id')->references('id')->on('farm_activities');
            $table->foreign('rate_card_id')->references('id')->on('rate_cards');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('farm_activity_items');
    }
};
