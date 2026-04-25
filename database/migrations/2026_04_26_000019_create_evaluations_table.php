<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('evaluations', function (Blueprint $table): void {
      $table->id();
      $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
      $table->foreignId('evaluator_id')->constrained('users');
      $table->foreignId('rubric_id')->constrained('rubrics')->cascadeOnDelete();
      $table->float('total_score');
      $table->text('notes')->nullable();
      $table->timestamp('created_at')->useCurrent();

      $table->unique(['project_id', 'evaluator_id'], 'uq_evaluation');
      $table->index('project_id');
      $table->index('evaluator_id');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('evaluations');
  }
};
