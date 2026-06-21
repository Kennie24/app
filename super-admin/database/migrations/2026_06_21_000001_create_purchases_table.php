<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 64)->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->string('method', 32);                 // mtn_momo | airtel_money | card | paypal
            $table->string('status', 24)->default('pending'); // pending | processing | succeeded | failed | cancelled
            $table->decimal('amount', 10, 2);
            $table->string('currency', 8)->default('USD');
            $table->string('msisdn', 32)->nullable();
            $table->string('email')->nullable();
            $table->string('provider', 32)->nullable();        // stripe | mtn | airtel
            $table->string('provider_ref')->nullable()->index(); // PaymentIntent ID / momo referenceId / airtel transactionId
            $table->string('client_secret')->nullable();       // for Stripe Elements
            $table->json('metadata')->nullable();
            $table->string('failure_reason')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['asset_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
