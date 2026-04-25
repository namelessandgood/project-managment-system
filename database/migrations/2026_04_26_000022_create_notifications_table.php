<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('notifications', function (Blueprint $table): void {
      $table->id();
      $table->foreignId('user_id')->constrained('users');
      $table->string('title');
      $table->text('message');
      $table->boolean('is_read')->default(false);
      $table->timestamp('created_at')->useCurrent();

      $table->index('user_id');
      $table->index(['user_id', 'is_read']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('notifications');
  }
};
