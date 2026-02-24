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
            $table->string('subtitle',255)->nullable()->after('title');
            $table->string('background_image',255)->nullable()->after('title');
            $table->string('address',255)->nullable()->after('subtitle');
            $table->string('postal_code',255)->nullable()->after('address');
            $table->string('city',255)->nullable()->after('postal_code');
            $table->string('phone_1',20)->nullable()->after('city');
            $table->string('phone_2',20)->nullable()->after('phone_1');
            $table->string('email',20)->nullable()->after('phone_2');
            $table->text('about_us')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('portal', function (Blueprint $table) {
            $table->dropColumn('subtitle');
            $table->dropColumn('background_image');
            $table->dropColumn('address');
            $table->dropColumn('postal_code');
            $table->dropColumn('city');
            $table->dropColumn('phone_1');
            $table->dropColumn('phone_2');
            $table->dropColumn('email');
            $table->dropColumn('about_us');
        });
    }
};
