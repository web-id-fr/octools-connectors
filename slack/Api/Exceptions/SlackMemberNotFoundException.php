<?php

namespace Webid\OctoolsSlack\Api\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SlackMemberNotFoundException extends Exception
{
    public function render(Request $request): JsonResponse
    {
        return response()->json(['error' => 'Slack member not found.'], status: 404);
    }
}
