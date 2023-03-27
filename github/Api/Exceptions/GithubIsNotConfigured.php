<?php

declare(strict_types=1);

namespace Webid\OctoolsGithub\Api\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GithubIsNotConfigured extends Exception
{
    public function render(Request $request): JsonResponse
    {
        return response()->json(['error' => 'Github is not configured for this workspace.'], status: 401);
    }
}
