<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('submissions', function (Blueprint $table): void {
      $table->id();
      $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
      $table->foreignId('milestone_id')->constrained('milestones')->cascadeOnDelete();
      $table->integer('version_number')->default(1);
      $table->string('file_path')->nullable();
      $table->string('file_type')->nullable();
      $table->string('link_url')->nullable();
      $table->foreignId('submitted_by')->constrained('users');
      $table->timestamp('created_at')->useCurrent();

      $table->unique(['project_id', 'milestone_id', 'version_number'], 'uq_submission_version');
      $table->index('project_id');
      $table->index('milestone_id');
    });

    DB::statement('ALTER TABLE submissions ADD CONSTRAINT chk_file_or_link CHECK (file_path IS NOT NULL OR link_url IS NOT NULL)');
  }

  public function down(): void
  {
    Schema::dropIfExists('submissions');
  }
};
