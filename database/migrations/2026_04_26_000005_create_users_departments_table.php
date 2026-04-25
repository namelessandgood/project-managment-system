<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('users_departments', function (Blueprint $table): void {
      $table->id();
      $table->foreignId('user_id')->constrained();
      $table->foreignId('department_id')->constrained()->cascadeOnDelete();

      $table->unique(['user_id', 'department_id'], 'uq_user_dept');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('users_departments');
  }
};
