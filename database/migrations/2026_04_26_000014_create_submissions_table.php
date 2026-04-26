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
    $submissionsExists = Schema::hasTable('submissions');

    if (! $submissionsExists) {
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
    }

    if (DB::getDriverName() === 'sqlite') {
      // SQLite does not support ALTER TABLE ... ADD CONSTRAINT for CHECK constraints.
      DB::statement(<<<'SQL'
CREATE TRIGGER IF NOT EXISTS chk_file_or_link_insert
BEFORE INSERT ON submissions
FOR EACH ROW
WHEN NEW.file_path IS NULL AND NEW.link_url IS NULL
BEGIN
  SELECT RAISE(ABORT, 'chk_file_or_link');
END;
SQL);

      DB::statement(<<<'SQL'
CREATE TRIGGER IF NOT EXISTS chk_file_or_link_update
BEFORE UPDATE ON submissions
FOR EACH ROW
WHEN NEW.file_path IS NULL AND NEW.link_url IS NULL
BEGIN
  SELECT RAISE(ABORT, 'chk_file_or_link');
END;
SQL);

      return;
    }

    if (! $submissionsExists) {
      DB::statement('ALTER TABLE submissions ADD CONSTRAINT chk_file_or_link CHECK (file_path IS NOT NULL OR link_url IS NOT NULL)');
    }
  }

  public function down(): void
  {
    if (DB::getDriverName() === 'sqlite') {
      DB::statement('DROP TRIGGER IF EXISTS chk_file_or_link_insert');
      DB::statement('DROP TRIGGER IF EXISTS chk_file_or_link_update');
    }

    Schema::dropIfExists('submissions');
  }
};
