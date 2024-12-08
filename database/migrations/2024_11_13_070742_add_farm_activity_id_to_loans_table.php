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
        Schema::table('loans', function (Blueprint $table) {
            $table->uuid('farm_activity_id')->nullable()->after('user_id');
            $table->foreign('farm_activity_id')->references('id')->on('farm_activities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign('loans_farm_activity_id_foreign');
            $table->dropColumn('farm_activity_id');
        });
    }
};
