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
        Schema::create('administration_level_threes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 5)->unique();
            $table->string('name');
            $table->uuid('administration_level_two_id');
            $table->softDeletes();
            $table->timestamps();

            //Foreign keys
            $table->foreign('administration_level_two_id')->references('id')->on('administration_level_twos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('administration_level_ones');
    }
};
