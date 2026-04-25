<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('project_topics', function (Blueprint $table): void {
      $table->id();
      $table->string('title');
      $table->text('description')->nullable();
      $table->foreignId('created_by')->constrained('users');
      $table->timestamp('created_at')->useCurrent();

      $table->index('created_by');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('project_topics');
  }
};
