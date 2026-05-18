<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('source_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('badge_id')->nullable()->constrained()->nullOnDelete();
            $table->string('submission_type', 16);
            $table->text('url')->nullable();
            $table->string('title')->nullable();
            $table->longText('content');
            $table->string('content_hash', 64)->nullable()->index();
            $table->string('category')->nullable()->index();
            $table->decimal('credibility_score', 5, 2)->nullable();
            $table->string('status', 32)->default('pending')->index();
            $table->foreignId('duplicate_of_id')->nullable()->constrained('articles')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
