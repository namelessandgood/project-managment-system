<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('supervisor_assignments', function (Blueprint $table): void {
      $table->id();
      $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
      $table->foreignId('supervisor_id')->constrained('users');
      $table->boolean('is_active')->default(true);
      $table->timestamp('assigned_at')->useCurrent();

      $table->unique(['project_id', 'supervisor_id'], 'uq_supervisor_assignment');
      $table->index('project_id');
      $table->index('supervisor_id');
      $table->index(['project_id', 'is_active']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('supervisor_assignments');
  }
};
