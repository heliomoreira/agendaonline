<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beta_signups', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 120);
            $table->string('email', 190)->unique();
            $table->string('negocio', 150)->nullable();
            $table->string('tipo_negocio', 40);
            $table->string('profissionais', 20)->nullable();
            $table->timestamp('consentimento_em');
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('convidado_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beta_signups');
    }
};
