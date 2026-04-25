<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateRubricRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'name' => ['sometimes', 'string', 'max:255'],
      'criteria' => ['sometimes', 'array', 'min:1'],
      'criteria.*.title' => ['required_with:criteria', 'string', 'max:255'],
      'criteria.*.weight_percentage' => ['required_with:criteria', 'numeric', 'min:0.01'],
    ];
  }

  public function withValidator(Validator $validator): void
  {
    $validator->after(function (Validator $validator): void {
      if (! $this->has('criteria')) {
        return;
      }

      $criteria = $this->input('criteria', []);
      $sum = collect($criteria)->sum(fn(array $item): float => (float) ($item['weight_percentage'] ?? 0));

      if (abs($sum - 100.0) > 0.0001) {
        $validator->errors()->add('criteria', 'The sum of criteria weight_percentage must equal 100.');
      }
    });
  }
}
