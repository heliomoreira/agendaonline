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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('phone_1_country_code')->nullable()->default('351')->after('phone_1');
            $table->string('phone_2_country_code')->nullable()->default('351')->after('phone_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['phone_1_country_code', 'phone_2_country_code']);
        });
    }
};
