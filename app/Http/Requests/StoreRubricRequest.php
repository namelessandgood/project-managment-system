<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreRubricRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'name' => ['required', 'string', 'max:255'],
      'criteria' => ['required', 'array', 'min:1'],
      'criteria.*.title' => ['required', 'string', 'max:255'],
      'criteria.*.weight_percentage' => ['required', 'numeric', 'min:0.01'],
    ];
  }

  public function withValidator(Validator $validator): void
  {
    $validator->after(function (Validator $validator): void {
      $criteria = $this->input('criteria', []);
      $sum = collect($criteria)->sum(fn(array $item): float => (float) ($item['weight_percentage'] ?? 0));

      if (abs($sum - 100.0) > 0.0001) {
        $validator->errors()->add('criteria', 'The sum of criteria weight_percentage must equal 100.');
      }
    });
  }
}
