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
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['credit_card', 'loan', 'mortgage', 'other']);
            $table->decimal('principal_amount', 12, 2)->nullable();
            $table->decimal('current_balance', 12, 2);
            $table->decimal('interest_rate', 5, 2); // Annual percentage rate
            $table->decimal('minimum_payment', 10, 2)->nullable();
            $table->integer('term_months')->nullable(); // Loan term in months
            $table->date('start_date');
            $table->date('payoff_target_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
