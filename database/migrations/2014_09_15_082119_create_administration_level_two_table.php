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
        Schema::create('administration_level_twos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code',12)->unique();
            $table->string('name');
            $table->uuid('country_id');
            $table->uuid('administration_level_one_id');
            $table->softDeletes();
            $table->timestamps();

            //Foreign keys
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->foreign('administration_level_one_id')->references('id')->on('administration_level_ones')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('administration_level_twos');
    }
};
