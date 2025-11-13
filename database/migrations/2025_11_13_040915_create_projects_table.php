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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('status')->default('planning'); // planning, in_progress, on_hold, completed, cancelled
            $table->string('type')->nullable(); // web_development, mobile_app, seo, consulting, etc.
            $table->decimal('budget', 12, 2)->nullable();
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->decimal('actual_hours', 8, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('deadline')->nullable();
            $table->date('completed_at')->nullable();
            $table->integer('progress')->default(0); // 0-100
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->string('repository_url')->nullable();
            $table->string('staging_url')->nullable();
            $table->string('production_url')->nullable();
            $table->json('technologies')->nullable(); // Array of tech stack
            $table->text('notes')->nullable();
            $table->boolean('is_billable')->default(true);
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
            $table->index('created_by');
            $table->index('status');
            $table->index('priority');
            $table->index('slug');
            $table->index('start_date');
            $table->index('deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
