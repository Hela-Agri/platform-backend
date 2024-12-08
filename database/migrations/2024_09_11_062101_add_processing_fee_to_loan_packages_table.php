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
        Schema::table('loan_packages', function (Blueprint $table) {
            //
            $table->decimal('processing_fee',8,2)->default(0.00);
            $table->string('processing_fee_desc')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_packages', function (Blueprint $table) {
            //
        });
    }
};
