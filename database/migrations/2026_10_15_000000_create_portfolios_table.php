<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 24)->unique();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->text('about')->nullable();
            $table->string('profile_picture')->nullable();
            $table->json('education')->nullable();
            $table->json('skills')->nullable();
            $table->json('projects')->nullable();
            $table->json('experience')->nullable();
            $table->json('links')->nullable();
            $table->string('template')->default('simple');
            $table->boolean('is_complete')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
