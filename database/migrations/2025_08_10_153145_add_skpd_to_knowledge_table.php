<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('knowledge', function (Blueprint $table) {
            $table->foreignId('skpd_id')->nullable()->constrained('users')->onDelete('set null');
            $table->index(['skpd_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('knowledge', function (Blueprint $table) {
            $table->dropIndex(['skpd_id']);
            $table->dropForeign(['skpd_id']);
            $table->dropColumn('skpd_id');
        });
    }
};
