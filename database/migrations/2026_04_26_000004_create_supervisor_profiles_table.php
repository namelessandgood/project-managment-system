<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('supervisor_profiles', function (Blueprint $table): void {
      $table->id();
      $table->foreignId('user_id')->constrained();
      $table->integer('max_projects')->default(5);

      $table->unique('user_id');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('supervisor_profiles');
  }
};
