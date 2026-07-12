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
        Schema::table('settings', function (Blueprint $table) {
            $table->time('sms_send_hour')->default('18:00:00')->after('booking_allow_overlap');
            $table->unsignedTinyInteger('sms_advance_days')->default(1)->after('sms_send_hour');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('sms_send_hour');
            $table->dropColumn('sms_advance_days');
        });
    }
};
