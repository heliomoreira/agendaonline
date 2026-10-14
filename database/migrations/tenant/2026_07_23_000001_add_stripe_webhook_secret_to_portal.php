<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portal', function (Blueprint $table) {
            $table->text('payment_stripe_webhook_secret')->nullable()->after('payment_stripe_secret');
        });
    }

    public function down(): void
    {
        Schema::table('portal', function (Blueprint $table) {
            $table->dropColumn('payment_stripe_webhook_secret');
        });
    }
};
