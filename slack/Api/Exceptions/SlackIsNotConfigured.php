<?php

namespace Webid\OctoolsSlack\Api\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SlackIsNotConfigured extends Exception
{
    public function render(Request $request): JsonResponse
    {
        return response()->json(['error' => 'Slack is not configured for this workspace.'], status: 401);
    }
}
