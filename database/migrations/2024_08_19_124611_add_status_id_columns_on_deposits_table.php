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
        Schema::table('deposits', function (Blueprint $table) {
            $table->decimal('allowed_amount', 16, 2)->default(0)->after('id');
            $table->decimal('balance', 16, 2)->default(0)->after('allowed_amount');
            $table->uuid('status_id')->after('amount')->nullable();
            $table->uuid('wallet_transaction_id')->nullable()->change();

            $table->foreign('status_id')->references('id')->on('statuses');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropForeign('deposits_status_id_foreign');
            $table->dropColumn('status_id');
            $table->dropColumn('allowed_amount');
        });
    }
};
