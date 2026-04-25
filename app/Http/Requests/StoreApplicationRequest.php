<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'topic_id' => ['required', 'exists:project_topics,id'],
      'group_id' => ['required', 'exists:groups,id'],
    ];
  }
}
