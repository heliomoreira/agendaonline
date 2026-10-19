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
        Schema::table('beta_signups', function (Blueprint $table) {
            $table->renameColumn('nome', 'name');
            $table->renameColumn('negocio', 'business');
            $table->renameColumn('tipo_negocio', 'business_type');
            $table->renameColumn('profissionais', 'professionals');
            $table->renameColumn('consentimento_em', 'consented_at');
            $table->renameColumn('convidado_em', 'invited_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beta_signups', function (Blueprint $table) {
            $table->renameColumn('name', 'nome');
            $table->renameColumn('business', 'negocio');
            $table->renameColumn('business_type', 'tipo_negocio');
            $table->renameColumn('professionals', 'profissionais');
            $table->renameColumn('consented_at', 'consentimento_em');
            $table->renameColumn('invited_at', 'convidado_em');
        });
    }
};
