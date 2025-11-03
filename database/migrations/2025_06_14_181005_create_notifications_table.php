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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->string('sender');
            $table->string('destinatary');
            $table->enum('type', ['sms', 'email']);
            $table->text('text');
            $table->date('service_day');
            $table->string('service_start_hour');
            $table->string('service_end_hour');
            $table->string('status')->default('scheduled');
            $table->timestamps();

            $table->foreign('tenant_id')
                ->references('id')
                ->on('tenants')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
