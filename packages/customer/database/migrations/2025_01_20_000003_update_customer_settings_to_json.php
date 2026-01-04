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
        // Drop old tables if exist
        Schema::dropIfExists('customer_setting_items');
        
        // Update customer_settings table to use JSON
        if (Schema::hasTable('customer_settings')) {
            Schema::table('customer_settings', function (Blueprint $table) {
                // Drop old columns if exist
                if (Schema::hasColumn('customer_settings', 'push_notifications')) {
                    $table->dropColumn('push_notifications');
                }
                if (Schema::hasColumn('customer_settings', 'email_notifications')) {
                    $table->dropColumn('email_notifications');
                }
            });
        } else {
            Schema::create('customer_settings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
                $table->json('settings')->nullable();
                $table->timestamps();

                $table->unique('customer_id');
            });
        }

        // Add settings column if not exists
        if (Schema::hasTable('customer_settings') && !Schema::hasColumn('customer_settings', 'settings')) {
            Schema::table('customer_settings', function (Blueprint $table) {
                $table->json('settings')->nullable()->after('customer_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_settings');
    }
};

