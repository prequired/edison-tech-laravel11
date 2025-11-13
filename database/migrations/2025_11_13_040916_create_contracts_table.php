<?php

declare(strict_types=1);

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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('contract_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('draft'); // draft, sent, signed, active, completed, cancelled
            $table->string('type')->default('fixed_price'); // fixed_price, hourly, retainer
            $table->decimal('value', 12, 2);
            $table->decimal('deposit_amount', 12, 2)->nullable();
            $table->boolean('deposit_paid')->default(false);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('signed_at')->nullable();
            $table->string('signed_document')->nullable(); // PDF path
            $table->string('signed_by_name')->nullable();
            $table->string('signed_by_email')->nullable();
            $table->string('signed_ip_address')->nullable();
            $table->text('terms')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
            $table->index('project_id');
            $table->index('contract_number');
            $table->index('status');
            $table->index('signed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
