<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('groups', function (Blueprint $table): void {
      $table->id();
      $table->string('name');
      $table->foreignId('created_by')->constrained('users');
      $table->string('status')->default('Proposed');
      $table->timestamp('created_at')->useCurrent();
      $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

      $table->index('created_by');
      $table->index('status');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('groups');
  }
};
