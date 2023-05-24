<?php

namespace Webid\OctoolsGryzzly\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeclarationParametersRequest extends CursorPaginatedRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            ...parent::rules(),
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ];
    }
}
