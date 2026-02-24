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
        Schema::table('portal', function (Blueprint $table) {
            $table->string('payment_stripe_key', 255)->nullable()->after('sunday_hours');
            $table->string('payment_stripe_secret', 255)->nullable()->after('payment_stripe_key');
            $table->string('payment_stripe_allow_card', 255)->nullable()->after('payment_stripe_secret');
            $table->string('payment_stripe_allow_multibanco', 255)->nullable()->after('payment_stripe_allow_card');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('portal', function (Blueprint $table) {
            $table->dropColumn('payment_stripe_key');
            $table->dropColumn('payment_stripe_secret');
            $table->dropColumn('payment_stripe_allow_card');
            $table->dropColumn('payment_stripe_allow_multibanco');
        });
    }
};
