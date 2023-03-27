<?php

declare(strict_types=1);

namespace Webid\OctoolsGithub\Api\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class CustomGithubMessageException extends Exception
{
    public function __construct(
        string $message,
    ) {
        parent::__construct($message);
    }
    public function render(Request $request): JsonResponse
    {
        return response()->json(['error' => $this->message], status: 404);
    }
}
