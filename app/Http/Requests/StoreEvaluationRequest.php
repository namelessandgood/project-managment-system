<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvaluationRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'rubric_id' => ['required', 'exists:rubrics,id'],
      'notes' => ['nullable', 'string'],
      'scores' => ['required', 'array', 'min:1'],
      'scores.*.criteria_id' => ['required', 'exists:rubric_criteria,id'],
      'scores.*.score' => ['required', 'numeric', 'min:0'],
    ];
  }
}
