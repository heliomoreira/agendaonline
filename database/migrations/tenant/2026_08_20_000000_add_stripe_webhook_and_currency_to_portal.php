<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portal', function (Blueprint $table) {
            $table->string('payment_stripe_webhook_secret', 255)->nullable()->after('payment_stripe_allow_multibanco');
            $table->string('payment_currency', 3)->default('eur')->after('payment_stripe_webhook_secret');
        });
    }

    public function down(): void
    {
        Schema::table('portal', function (Blueprint $table) {
            $table->dropColumn(['payment_stripe_webhook_secret', 'payment_currency']);
        });
    }
};
