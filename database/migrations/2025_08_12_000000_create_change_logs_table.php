<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('change_logs', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type'); // Knowledge, User, Category, etc
            $table->unsignedBigInteger('entity_id');
            $table->string('action'); // created, updated, deleted, published, archived
            $table->string('field_name')->nullable(); // field yang berubah
            $table->text('old_value')->nullable(); // nilai lama
            $table->text('new_value')->nullable(); // nilai baru
            $table->text('description')->nullable(); // deskripsi perubahan
            $table->json('metadata')->nullable(); // data tambahan
            $table->unsignedBigInteger('user_id')->nullable(); // user yang melakukan perubahan
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('changed_at');
            $table->timestamps();

            // Indexes
            $table->index(['entity_type', 'entity_id']);
            $table->index(['user_id']);
            $table->index(['action']);
            $table->index(['changed_at']);
            $table->index(['created_at']);

            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('change_logs');
    }
};