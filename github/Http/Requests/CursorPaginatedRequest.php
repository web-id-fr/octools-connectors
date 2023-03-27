<?php

declare(strict_types=1);

namespace Webid\OctoolsGithub\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CursorPaginatedRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'cursor' => ['nullable', 'string'],
        ];
    }
}
