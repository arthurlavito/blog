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
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // This single line creates both 'likeable_id' (bigint) and 'likeable_type' (string)
            // It handles everything for both Posts and Comments automatically.
            $table->morphs('likeable'); 
            
            $table->timestamps();
            
            // Anti-spam: ensures a user can only like a specific post or comment once
            $table->unique(['user_id', 'likeable_id', 'likeable_type']);
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
