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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_online')->default(false)->after('password');
            $table->timestamp('last_seen_at')->nullable()->after('is_online');
            $table->timestamp('last_login_at')->nullable()->after('last_seen_at');
            $table->timestamp('last_logout_at')->nullable()->after('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_online', 'last_seen_at', 'last_login_at', 'last_logout_at']);
        });
    }
};
