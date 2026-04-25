<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMilestoneRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'title' => ['sometimes', 'string', 'max:255'],
      'description' => ['nullable', 'string'],
      'due_date' => ['sometimes', 'date', 'after:today'],
    ];
  }
}
