<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('milestones', function (Blueprint $table): void {
      $table->id();
      $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
      $table->string('title');
      $table->text('description')->nullable();
      $table->timestamp('due_date');
      $table->foreignId('created_by')->constrained('users');
      $table->timestamp('created_at')->useCurrent();

      $table->index('project_id');
      $table->index('due_date');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('milestones');
  }
};
