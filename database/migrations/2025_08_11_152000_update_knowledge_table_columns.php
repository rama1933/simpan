<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('knowledge', function (Blueprint $table) {
            // Add published_at if missing
            if (!Schema::hasColumn('knowledge', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('status');
            }

            // Drop legacy category column if exists (replaced by category_id)
            if (Schema::hasColumn('knowledge', 'category')) {
                $table->dropIndexIfExists('knowledge_category_index');
                $table->dropColumn('category');
            }
        });
    }

    public function down(): void
    {
        Schema::table('knowledge', function (Blueprint $table) {
            if (Schema::hasColumn('knowledge', 'published_at')) {
                $table->dropColumn('published_at');
            }
            if (!Schema::hasColumn('knowledge', 'category')) {
                $table->string('category')->nullable();
                $table->index('category');
            }
        });
    }
};


