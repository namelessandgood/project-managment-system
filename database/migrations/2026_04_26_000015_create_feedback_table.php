<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('feedback', function (Blueprint $table): void {
      $table->id();
      $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
      $table->foreignId('user_id')->constrained('users');
      $table->text('comment');
      $table->boolean('is_private')->default(false);
      $table->timestamp('created_at')->useCurrent();

      $table->index('submission_id');
      $table->index(['submission_id', 'is_private']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('feedback');
  }
};
