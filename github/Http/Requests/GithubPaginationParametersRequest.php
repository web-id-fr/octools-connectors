<?php

declare(strict_types=1);

namespace Webid\OctoolsGithub\Http\Requests;

use Illuminate\Validation\Rule;

class GithubPaginationParametersRequest extends CursorPaginatedRequest
{
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'sort' => ['string', Rule::in(['created_at', 'updated_at'])],
            'direction' => ['string', Rule::in(['asc', 'desc'])],
        ];
    }
}
