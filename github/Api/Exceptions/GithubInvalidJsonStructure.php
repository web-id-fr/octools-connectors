<?php

declare(strict_types=1);

namespace Webid\OctoolsGithub\Api\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GithubInvalidJsonStructure extends Exception
{
    public function render(Request $request): JsonResponse
    {
        return response()->json(['error' => 'Wrong JSON structure in Github API response.'], status: 404);
    }
}
