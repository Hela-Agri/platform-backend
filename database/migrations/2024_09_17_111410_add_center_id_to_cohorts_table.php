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
        Schema::table('cohorts', function (Blueprint $table) {
            //
            $table->uuid('center_id')->after('description')->nullabe();
            $table->foreign('center_id')->references('id')->on('centers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cohorts', function (Blueprint $table) {
            //
        });
    }
};
