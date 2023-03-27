<?php

declare(strict_types=1);

namespace Webid\OctoolsGryzzly\Api\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GryzzlyIsNotConfigured extends Exception
{
    public function render(Request $request): JsonResponse
    {
        return response()->json(['error' => 'Gryzzly is not configured for this workspace.'], status: 401);
    }
}
