<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('knowledge_tag')) {
            Schema::create('knowledge_tag', function (Blueprint $table) {
                $table->id();
                $table->foreignId('knowledge_id')->constrained('knowledge')->onDelete('cascade');
                $table->foreignId('tag_id')->constrained('tags')->onDelete('cascade');
                $table->timestamps();
                $table->unique(['knowledge_id', 'tag_id']);
                $table->index(['tag_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_tag');
    }
};
