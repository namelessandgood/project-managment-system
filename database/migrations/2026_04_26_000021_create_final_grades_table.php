<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('final_grades', function (Blueprint $table): void {
      $table->id();
      $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
      $table->float('supervisor_score');
      $table->float('jury_average_score');
      $table->float('final_score');
      $table->timestamp('calculated_at')->useCurrent();
      $table->timestamp('recalculated_at')->nullable();

      $table->unique('project_id');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('final_grades');
  }
};
