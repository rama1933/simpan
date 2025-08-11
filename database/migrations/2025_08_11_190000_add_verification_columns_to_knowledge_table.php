<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('knowledge')) {
            Schema::table('knowledge', function (Blueprint $table) {
                if (!Schema::hasColumn('knowledge', 'verification_status')) {
                    $table->enum('verification_status', ['pending', 'approved', 'rejected'])->default('pending')->after('status');
                }
                if (!Schema::hasColumn('knowledge', 'verified_by')) {
                    $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete()->after('verification_status');
                }
                if (!Schema::hasColumn('knowledge', 'verified_at')) {
                    $table->timestamp('verified_at')->nullable()->after('verified_by');
                }
                if (!Schema::hasColumn('knowledge', 'verification_note')) {
                    $table->string('verification_note', 500)->nullable()->after('verified_at');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('knowledge')) {
            Schema::table('knowledge', function (Blueprint $table) {
                if (Schema::hasColumn('knowledge', 'verification_note')) {
                    $table->dropColumn('verification_note');
                }
                if (Schema::hasColumn('knowledge', 'verified_at')) {
                    $table->dropColumn('verified_at');
                }
                if (Schema::hasColumn('knowledge', 'verified_by')) {
                    $table->dropConstrainedForeignId('verified_by');
                }
                if (Schema::hasColumn('knowledge', 'verification_status')) {
                    $table->dropColumn('verification_status');
                }
            });
        }
    }
};
