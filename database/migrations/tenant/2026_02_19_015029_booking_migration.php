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
        Schema::table('agenda', function (Blueprint $table) {
            // Campos de pagamento Stripe
            $table->string('payment_intent_id')->nullable()->after('notes');
            $table->string('payment_method')->nullable()->after('payment_intent_id'); // 'card', 'multibanco', 'mbway'
            $table->boolean('paid')->default(false)->after('payment_method');
            $table->decimal('amount', 10, 2)->nullable()->after('paid');

            // Status da marcação
            $table->enum('status', [
                'pending',          // Aguardando confirmação
                'confirmed',        // Confirmada (pago ou sem pagamento)
                'payment_pending',  // Aguardando pagamento (Multibanco)
                'payment_failed',   // Pagamento falhou
                'completed',        // Serviço completado
                'cancelled',        // Cancelada
                'no_show'          // Cliente não compareceu
            ])->default('pending')->after('amount');

            // Dados do cliente (para bookings sem login)
            $table->string('client_name')->nullable()->after('client_id');
            $table->string('client_email')->nullable()->after('client_name');
            $table->string('client_phone_1')->nullable()->after('client_email');
            $table->string('client_phone_2')->nullable()->after('client_phone_1');

            // Metadata útil
            $table->timestamp('confirmed_at')->nullable()->after('status');
            $table->timestamp('cancelled_at')->nullable()->after('confirmed_at');
            $table->string('cancellation_reason')->nullable()->after('cancelled_at');
            $table->string('cancelled_by')->nullable()->after('cancellation_reason'); // 'client', 'professional', 'admin'

            // Índices para performance
            $table->index('payment_intent_id');
            $table->index('status');
            $table->index('day');
            $table->index(['professional_id', 'day']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            $table->dropIndex(['agenda_payment_intent_id_index']);
            $table->dropIndex(['agenda_status_index']);
            $table->dropIndex(['agenda_day_index']);
            $table->dropIndex(['agenda_professional_id_day_index']);

            $table->dropColumn([
                'payment_intent_id',
                'payment_method',
                'paid',
                'amount',
                'status',
                'client_name',
                'client_email',
                'client_phone_1',
                'client_phone_2',
                'confirmed_at',
                'cancelled_at',
                'cancellation_reason',
                'cancelled_by',
            ]);
        });
    }
};
