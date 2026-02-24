<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE portal ROW_FORMAT=DYNAMIC');

        Schema::table('portal', function (Blueprint $table) {
            $table->string('subtitle', 255)->nullable()->after('title');
            $table->string('background_image', 255)->nullable()->after('subtitle');
            $table->string('address', 255)->nullable()->after('subtitle');
            $table->string('postal_code', 10)->nullable()->after('address');
            $table->string('city', 100)->nullable()->after('postal_code');
            $table->string('phone_1', 20)->nullable()->after('city');
            $table->string('phone_2', 20)->nullable()->after('phone_1');
            $table->string('email', 255)->nullable()->after('phone_2');
            $table->text('about_us')->nullable()->after('email');
            $table->string('button_background_color', 10)->nullable()->after('main_color');
            $table->string('button_text_color', 10)->nullable()->after('button_background_color');
            $table->boolean('requires_payment')->default(false)->after('button_text_color');
            $table->integer('payment_percentage')->nullable()->after('requires_payment');
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
            $table->dropColumn('button_background_color');
            $table->dropColumn('button_text_color');
            $table->dropColumn('requires_payment');
            $table->dropColumn('payment_percentage');
        });
    }
};
