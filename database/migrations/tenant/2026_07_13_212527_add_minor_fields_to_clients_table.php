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
            $table->boolean('is_minor')->default(false)->after('birthdate');
            $table->string('parent_name')->nullable()->after('is_minor');
            $table->string('parent_phone_1_country_code')->nullable()->after('parent_name');
            $table->string('parent_phone_1')->nullable()->after('parent_phone_1_country_code');
            $table->string('parent_email')->nullable()->after('parent_phone_1');
            $table->text('parent_notes')->nullable()->after('parent_email');
            $table->boolean('send_sms_to_parent')->default(false)->after('parent_notes');
            $table->unsignedBigInteger('parent_sms_template_id')->nullable()->after('send_sms_to_parent');

            $table->foreign('parent_sms_template_id')
                ->references('id')->on('sms_templates')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(['parent_sms_template_id']);
            $table->dropColumn([
                'is_minor',
                'parent_name',
                'parent_phone_1_country_code',
                'parent_phone_1',
                'parent_email',
                'parent_notes',
                'send_sms_to_parent',
                'parent_sms_template_id',
            ]);
        });
    }
};
