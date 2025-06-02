<?php

use App\Models\User;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->string('currency')->nullable();
            $table->string('amount')->nullable();
            $table->string('crypto_value')->nullable();
            $table->enum('status', ['pending', 'failed', 'success'])->nullable();
            $table->string('trans_id')->nullable();
            $table->enum('trans_type', ['deposit', 'withdraw'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
