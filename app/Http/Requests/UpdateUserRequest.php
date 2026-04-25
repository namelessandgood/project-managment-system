<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'name' => ['sometimes', 'string', 'max:255'],
      'email' => [
        'sometimes',
        'email',
        Rule::unique('users', 'email')->ignore((int) $this->route('id')),
      ],
      'password' => ['sometimes', 'string', 'min:8'],
    ];
  }
}
