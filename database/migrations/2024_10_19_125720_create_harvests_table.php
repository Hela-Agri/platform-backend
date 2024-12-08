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
        Schema::create('harvests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->double('weight')->default(0);
            $table->string('unit_id');
            $table->string('farm_activity_id');
            $table->dateTime('harvest_date');
            $table->uuid('user_id');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('unit_id')->references('id')->on('units');
            $table->foreign('farm_activity_id')->references('id')->on('farm_activities');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harvests');
    }
};
