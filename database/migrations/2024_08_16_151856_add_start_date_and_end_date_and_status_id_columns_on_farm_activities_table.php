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
        Schema::table('farm_activities', function (Blueprint $table) {
            $table->dateTime('start_date')->nullable()->after('wallet_id');
            $table->dateTime('end_date')->nullable()->after('start_date');
            $table->uuid('status_id')->nullable()->after('user_id');

            $table->foreign('status_id')->references('id')->on('statuses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('farm_activities', function (Blueprint $table) {
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
            $table->dropColumn('status_id');
            $table->dropForeign('farm_activities_status_id_foreign');
        });
    }
};
