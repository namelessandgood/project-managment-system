<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'group_id' => ['required', 'exists:groups,id'],
      'topic_id' => ['nullable', 'exists:project_topics,id'],
      'title' => ['required', 'string', 'max:255'],
      'abstract' => ['nullable', 'string'],
      'objectives' => ['nullable', 'string'],
      'tech_stack' => ['nullable', 'string'],
    ];
  }
}
