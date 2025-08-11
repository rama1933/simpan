<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('knowledge_attachments')) {
            Schema::create('knowledge_attachments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('knowledge_id')->constrained('knowledge')->onDelete('cascade');
                $table->string('original_name');
                $table->string('path');
                $table->string('mime_type')->nullable();
                $table->unsignedBigInteger('size_bytes')->default(0);
                $table->string('disk')->default('public');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_attachments');
    }
};
