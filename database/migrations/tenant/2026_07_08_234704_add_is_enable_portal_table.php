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
            $table->boolean('enable_portal')->default(true)->after('secondary_color');
            $table->boolean('enable_booking')->default(true)->after('enable_portal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('portal', function (Blueprint $table) {
            $table->dropColumn('enable_portal');
            $table->dropColumn('enable_booking');
        });
    }
};
