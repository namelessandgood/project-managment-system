<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('evaluation_details', function (Blueprint $table): void {
      $table->id();
      $table->foreignId('evaluation_id')->constrained('evaluations')->cascadeOnDelete();
      $table->foreignId('criteria_id')->constrained('rubric_criteria')->cascadeOnDelete();
      $table->float('score');

      $table->unique(['evaluation_id', 'criteria_id'], 'uq_eval_criteria');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('evaluation_details');
  }
};
