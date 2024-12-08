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
        Schema::create('administration_level_ones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 5)->unique();
            $table->string('name');
            $table->uuid('country_id');
            $table->softDeletes();
            $table->timestamps();

            //Foreign keys
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
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
