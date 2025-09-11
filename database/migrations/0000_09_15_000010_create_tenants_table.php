<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name', 255);
            $table->string('vat', 20)->nullable();
            $table->string('address', 150)->nullable();
            $table->string('number_port', 20)->nullable();
            $table->string('zip_code', 20)->nullable();
            $table->string('county', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('phone_1', 20)->nullable();
            $table->string('phone_2', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->boolean('sms_status')->default(false);
            $table->string('sms_sender', 11)->nullable();
            $table->unsignedInteger('sms_credits')->default(0);
            $table->boolean('is_beta')->default(false);
            $table->string('storage_token', 100)->nullable();
            $table->unsignedInteger('storage_quota')->default(0);
            $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete();
            $table->date('plan_end_date')->nullable();
            $table->boolean('booking_available')->default(true);
            $table->boolean('status')->default(true);
            $table->text('data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
}
