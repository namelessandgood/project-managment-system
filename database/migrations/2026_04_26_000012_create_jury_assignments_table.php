<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('jury_assignments', function (Blueprint $table): void {
      $table->id();
      $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
      $table->foreignId('evaluator_id')->constrained('users');
      $table->timestamp('assigned_at')->useCurrent();

      $table->unique(['project_id', 'evaluator_id'], 'uq_jury_assignment');
      $table->index('project_id');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('jury_assignments');
  }
};
