<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('rubric_criteria', function (Blueprint $table): void {
      $table->id();
      $table->foreignId('rubric_id')->constrained('rubrics')->cascadeOnDelete();
      $table->string('title');
      $table->float('weight_percentage');

      $table->index('rubric_id');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('rubric_criteria');
  }
};
