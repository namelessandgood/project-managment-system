<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendNotificationRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'user_id' => ['required_without:user_ids', 'exists:users,id'],
      'user_ids' => ['nullable', 'array', 'min:1'],
      'user_ids.*' => ['exists:users,id'],
      'title' => ['required', 'string', 'max:255'],
      'message' => ['required', 'string'],
    ];
  }
}
