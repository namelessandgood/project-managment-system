<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('group_members', function (Blueprint $table): void {
      $table->id();
      $table->foreignId('group_id')->constrained()->cascadeOnDelete();
      $table->foreignId('user_id')->constrained();

      $table->unique(['group_id', 'user_id'], 'uq_group_member');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('group_members');
  }
};
