<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlagiarismReportRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'report_file' => ['required', 'file'],
      'similarity_score' => ['nullable', 'numeric', 'between:0,100'],
      'notes' => ['nullable', 'string'],
    ];
  }
}
