<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('knowledge', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['author_id']);
            
            // Modify the column to be nullable
            $table->foreignId('author_id')->nullable()->change();
            
            // Re-add the foreign key constraint
            $table->foreign('author_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('knowledge', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['author_id']);
            
            // Make the column not nullable again
            $table->foreignId('author_id')->change();
            
            // Re-add the foreign key constraint with cascade delete
            $table->foreign('author_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};