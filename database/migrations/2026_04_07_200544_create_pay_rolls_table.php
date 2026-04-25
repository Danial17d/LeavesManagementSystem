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
        Schema::create('payroll_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->tinyInteger('month');
            $table->year('year');
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('unpaid_deduction', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2)->default(0);
            $table->string('status')->default('draft');
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pay_rolls');
    }
};
