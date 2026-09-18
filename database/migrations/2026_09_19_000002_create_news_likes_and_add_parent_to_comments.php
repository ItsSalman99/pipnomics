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
        // 1. Create news_article_likes table
        if (!Schema::hasTable('news_article_likes')) {
            Schema::create('news_article_likes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('news_article_id')->constrained('news_articles')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['news_article_id', 'user_id']);
            });
        }

        // 2. Add parent_id to news_comments for threaded replies
        if (Schema::hasTable('news_comments') && !Schema::hasColumn('news_comments', 'parent_id')) {
            Schema::table('news_comments', function (Blueprint $table) {
                $table->foreignId('parent_id')->nullable()->after('user_id')->constrained('news_comments')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('news_comments') && Schema::hasColumn('news_comments', 'parent_id')) {
            Schema::table('news_comments', function (Blueprint $table) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            });
        }

        Schema::dropIfExists('news_article_likes');
    }
};
