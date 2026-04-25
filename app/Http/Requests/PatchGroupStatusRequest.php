<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\GroupStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatchGroupStatusRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'status' => ['required', Rule::enum(GroupStatus::class)],
    ];
  }
}
