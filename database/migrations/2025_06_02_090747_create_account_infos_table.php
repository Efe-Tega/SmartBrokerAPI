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
        Schema::create('account_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->integer('acct_bal')->nullable();
            $table->integer('profit_bal')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('acct_name')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('btc_wallet')->nullable();
            $table->string('eth_wallet')->nullable();
            $table->string('usdt_wallet')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_infos');
    }
};
