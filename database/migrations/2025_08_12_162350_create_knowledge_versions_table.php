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
        Schema::create('knowledge_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('knowledge_id')->constrained()->onDelete('cascade');
            $table->integer('version_number');
            $table->string('title');
            $table->longText('content');
            $table->text('summary')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('skpd_id')->nullable()->constrained('master_skpds')->onDelete('set null');
            $table->enum('status', ['draft', 'published', 'archived', 'withdrawn', 'replaced'])->default('draft');
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('verification_notes')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->enum('change_type', ['created', 'updated', 'status_changed', 'withdrawn', 'replaced'])->default('created');
            $table->text('change_reason')->nullable();
            $table->json('metadata')->nullable(); // Store additional version metadata
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('replaced_by_version_id')->nullable()->constrained('knowledge_versions')->onDelete('set null');
            $table->timestamp('effective_date')->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->timestamps();
            
            // Indexes
            $table->index(['knowledge_id', 'version_number']);
            $table->index(['knowledge_id', 'is_current']);
            $table->index(['status', 'verification_status']);
            $table->index('effective_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_versions');
    }
};
