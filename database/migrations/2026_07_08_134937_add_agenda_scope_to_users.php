<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Se ligado, o utilizador só vê a agenda do profissional associado.
            $table->boolean('only_own_agenda')->default(false)->after('tenant_id');

            // Profissional (da BD do tenant) a que este utilizador corresponde.
            // Sem foreign key: a tabela professionals está noutra base de dados.
            $table->unsignedBigInteger('professional_id')->nullable()->after('only_own_agenda');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['only_own_agenda', 'professional_id']);
        });
    }
};
