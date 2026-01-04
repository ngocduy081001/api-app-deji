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
        Schema::table('refresh_tokens', function (Blueprint $table) {
            // Make token column nullable (old column, not used in new code)
            if (Schema::hasColumn('refresh_tokens', 'token')) {
                $table->string('token', 500)->nullable()->change();
            }
            
            // Add device_id column if it doesn't exist
            if (!Schema::hasColumn('refresh_tokens', 'device_id')) {
                $table->foreignId('device_id')->nullable()->after('user_id')->constrained('devices')->onDelete('cascade');
            }
            
            // Add revoked_at column if it doesn't exist
            if (!Schema::hasColumn('refresh_tokens', 'revoked_at')) {
                $table->timestamp('revoked_at')->nullable()->after('expires_at');
            }
            
            // Add token_hash column if it doesn't exist (new column for hashed tokens)
            if (!Schema::hasColumn('refresh_tokens', 'token_hash')) {
                $table->string('token_hash', 255)->nullable()->after('token');
            }
        });
        
        // Add index separately (can't add index in modify table callback)
        if (Schema::hasColumn('refresh_tokens', 'device_id')) {
            Schema::table('refresh_tokens', function (Blueprint $table) {
                $table->index(['user_id', 'device_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('refresh_tokens', function (Blueprint $table) {
            // Drop index
            if (Schema::hasColumn('refresh_tokens', 'device_id')) {
                $table->dropIndex(['user_id', 'device_id']);
            }
            
            // Remove columns
            if (Schema::hasColumn('refresh_tokens', 'token_hash')) {
                $table->dropColumn('token_hash');
            }
            
            if (Schema::hasColumn('refresh_tokens', 'revoked_at')) {
                $table->dropColumn('revoked_at');
            }
            
            if (Schema::hasColumn('refresh_tokens', 'device_id')) {
                $table->dropForeign(['device_id']);
                $table->dropColumn('device_id');
            }
        });
    }
};
