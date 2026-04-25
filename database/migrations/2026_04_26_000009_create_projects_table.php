<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('projects', function (Blueprint $table): void {
      $table->id();
      $table->foreignId('group_id')->constrained()->cascadeOnDelete();
      $table->foreignId('topic_id')->nullable()->constrained('project_topics')->cascadeOnDelete();
      $table->string('title');
      $table->text('abstract')->nullable();
      $table->text('objectives')->nullable();
      $table->string('tech_stack')->nullable();
      $table->string('status')->default('Pending');
      $table->timestamp('created_at')->useCurrent();
      $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

      $table->unique('group_id');
      $table->index('topic_id');
      $table->index('status');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('projects');
  }
};
