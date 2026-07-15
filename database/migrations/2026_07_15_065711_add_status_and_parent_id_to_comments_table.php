<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->after('body');
            $table->foreignId('parent_id')->nullable()->after('id')
                ->constrained('comments')->nullOnDelete();
        });

        // Existing comments predate moderation — approve them all.
        DB::table('comments')->whereNull('status')->orWhere('status', 'pending')->update(['status' => 'approved']);
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['status', 'parent_id']);
        });
    }
};
