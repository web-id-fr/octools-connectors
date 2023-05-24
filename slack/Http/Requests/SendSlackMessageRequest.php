<?php

declare(strict_types=1);

namespace Webid\OctoolsSlack\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendSlackMessageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'message' => ['required', 'string'],
            'channel' => ['required', 'string'],
            'blocks' => ['nullable', 'string'],
            'attachments' => ['nullable', 'string'],
        ];
    }
}
