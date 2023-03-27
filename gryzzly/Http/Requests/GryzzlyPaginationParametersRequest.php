<?php

namespace Webid\OctoolsGryzzly\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GryzzlyPaginationParametersRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'cursor' => ['nullable', 'integer'],
        ];
    }
}
