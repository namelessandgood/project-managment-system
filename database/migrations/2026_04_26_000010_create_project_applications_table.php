<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('project_applications', function (Blueprint $table): void {
      $table->id();
      $table->foreignId('topic_id')->constrained('project_topics')->cascadeOnDelete();
      $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
      $table->string('status')->default('Pending');
      $table->timestamp('applied_at')->useCurrent();

      $table->unique(['topic_id', 'group_id'], 'uq_topic_application');
      $table->index('group_id');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('project_applications');
  }
};
