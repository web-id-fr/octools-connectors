<?php

declare(strict_types=1);

namespace Webid\OctoolsGryzzly\Api\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GryzzlyInvalidJsonStructure extends Exception
{
    public function render(Request $request): JsonResponse
    {
        return response()->json(['error' => 'Wrong JSON structure in Gryzzly API response.'], status: 404);
    }
}
