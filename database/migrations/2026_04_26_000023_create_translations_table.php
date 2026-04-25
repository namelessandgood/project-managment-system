<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('translations', function (Blueprint $table): void {
      $table->id();
      $table->string('key');
      $table->string('language');
      $table->text('value');

      $table->unique(['key', 'language'], 'uq_translation');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('translations');
  }
};
