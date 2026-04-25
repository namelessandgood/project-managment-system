<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('users', function (Blueprint $table): void {
      $table->id();
      $table->string('name');
      $table->string('email');
      $table->string('password');
      $table->foreignId('role_id')->constrained()->cascadeOnDelete();
      $table->boolean('is_active')->default(true);
      $table->timestamp('created_at')->useCurrent();
      $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

      $table->unique('email');
      $table->index('role_id');
      $table->index('is_active');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('users');
  }
};
