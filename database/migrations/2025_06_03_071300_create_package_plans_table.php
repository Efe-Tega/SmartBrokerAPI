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
        Schema::create('package_plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_name')->unique();
            $table->decimal('min_amount', 8, 2)->nullable();
            $table->decimal('max_amount', 8, 2)->nullable();
            $table->integer('roi')->nullable();
            $table->integer('duration')->nullable();
            $table->integer('total_return')->nullable();
            $table->text('features')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_plans');
    }
};
