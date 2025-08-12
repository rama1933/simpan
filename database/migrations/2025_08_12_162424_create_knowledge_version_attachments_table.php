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
        Schema::create('knowledge_version_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('knowledge_version_id')->constrained()->onDelete('cascade');
            $table->string('filename');
            $table->string('original_filename');
            $table->string('file_path');
            $table->string('file_type');
            $table->bigInteger('file_size');
            $table->string('mime_type');
            $table->text('description')->nullable();
            $table->enum('attachment_type', ['document', 'image', 'video', 'audio', 'other'])->default('document');
            $table->boolean('is_primary')->default(false);
            $table->json('metadata')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Indexes
            $table->index(['knowledge_version_id', 'attachment_type'], 'kva_version_type_idx');
            $table->index('is_primary', 'kva_primary_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_version_attachments');
    }
};
