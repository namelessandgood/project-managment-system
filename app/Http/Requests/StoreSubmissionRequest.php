<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\FileType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreSubmissionRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'project_id' => ['required', 'exists:projects,id'],
      'file_path' => ['nullable', 'string'],
      'file_type' => ['nullable', Rule::enum(FileType::class)],
      'link_url' => ['nullable', 'url'],
    ];
  }

  public function withValidator(Validator $validator): void
  {
    $validator->after(function (Validator $validator): void {
      $hasFile = filled($this->input('file_path'));
      $hasLink = filled($this->input('link_url'));

      if ($hasFile === $hasLink) {
        $validator->errors()->add('file_path', 'Exactly one of file_path or link_url must be provided.');
      }
    });
  }
}
