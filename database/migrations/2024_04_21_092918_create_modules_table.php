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
        Schema::create('modules', function (Blueprint $table) {
            $table->uuid('id')->primary;
            $table->text('name');

            $table->boolean('has_approve');
            $table->boolean('deactivate')->default(false)->nullable();
            $table->boolean('activate')->default(false)->nullable();
            $table->boolean('has_download')->default(false)->nullable();
            $table->boolean('upload')->default(false)->nullable();
            $table->boolean('has_print')->default(false)->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
