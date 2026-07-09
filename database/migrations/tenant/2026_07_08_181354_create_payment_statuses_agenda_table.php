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
        Schema::table('agenda', function (Blueprint $table) {
            $table->foreignId('payment_status_id')
                ->nullable()
                ->constrained('payment_statuses')
                ->nullOnDelete()->after('payment_status_id');
            $table->timestamp('paid_at')->nullable()->after('payment_status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            $table->dropForeign(['payment_status_id']);
            $table->dropForeign(['paid_at']);
        });
    }
};
