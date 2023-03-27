<?php

declare(strict_types=1);

namespace Webid\OctoolsGryzzly\Api\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeNotFoundException extends Exception
{
    public function render(Request $request): JsonResponse
    {
        return response()->json(['error' => 'Employee not found.'], status: 404);
    }
}
