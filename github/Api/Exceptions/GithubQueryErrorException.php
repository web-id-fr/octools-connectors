<?php

declare(strict_types=1);

namespace Webid\OctoolsGithub\Api\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GithubQueryErrorException extends Exception
{
    public static function fromErrorResponse(array $errors): self
    {
        $messages = array_filter(array_map(
            function (mixed $item) {
                if (is_array($item)) {
                    $message = null;
                    if (isset($item['message']) && is_string($item['message'])) {
                        $message = $item['message'];

                        if (isset($item['path']) && is_array($item['path'])) {
                            $message .= '(path : ' . join('/', $item['path']).')';
                        }
                    }

                    return $message;
                } elseif (is_string($item)) {
                    return $item;
                }

                return null;
            },
            $errors
        ));

        return new self("Github API respond with errors : \n" . join("\n", $messages));
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json(['error' => $this->getMessage()], status: 400);
    }
}
