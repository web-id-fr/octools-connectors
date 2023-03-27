<?php

declare(strict_types=1);

namespace Webid\OctoolsGithub\Api\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class RepositoryNotFoundException extends Exception
{
    public function render(Request $request): JsonResponse
    {
        return response()->json(['error' => 'Repository not found.'], status: 404);
    }
}
