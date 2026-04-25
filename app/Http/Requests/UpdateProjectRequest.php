<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'abstract' => ['nullable', 'string'],
      'objectives' => ['nullable', 'string'],
      'tech_stack' => ['nullable', 'string'],
    ];
  }
}
