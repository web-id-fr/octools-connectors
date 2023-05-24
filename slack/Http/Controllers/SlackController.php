<?php

declare(strict_types=1);

namespace Webid\OctoolsSlack\Http\Controllers;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use JoliCode\Slack\Exception\SlackErrorResponse;
use Webid\Octools\Models\Application;
use Webid\OctoolsSlack\Api\Entities\SlackCredentials;
use Webid\OctoolsSlack\Api\Exceptions\SlackIsNotConfigured;
use Webid\OctoolsSlack\Http\Requests\SendSlackMessageRequest;
use Webid\OctoolsSlack\Http\Requests\SlackPaginationParametersRequest;
use Webid\OctoolsSlack\OctoolsSlack;
use Webid\OctoolsSlack\Services\SlackServiceDecorator;

class SlackController
{
    public function __construct(
        private readonly SlackServiceDecorator $client,
    ) {
    }

    /**
     * @throws AuthenticationException
     * @throws SlackIsNotConfigured
     */
    public function getCompanyEmployees(SlackPaginationParametersRequest $request): JsonResponse
    {
        $credentials = $this->getApplicationSlackCredentials(loggedApplication());

        /** @var array $parameters */
        $parameters = $request->validated();

        return response()->json($this->client->getEmployees($credentials, $parameters));
    }

    /**
     * @throws AuthenticationException
     * @throws SlackIsNotConfigured
     */
    public function sendMessageToChannel(SendSlackMessageRequest $request): JsonResponse
    {
        /** @var string $message */
        $message = $request->validated('message');

        /** @var string $channel */
        $channel = $request->validated('channel');

        /** @var string $blocks */
        $blocks = $request->validated('blocks');

        /** @var string $attachments */
        $attachments = $request->validated('attachments');
        try {
            $credentials = $this->getApplicationSlackCredentials(loggedApplication());
            $this->client->sendMessageToChannel($credentials, $message, $channel, $blocks, $attachments);
        } catch (SlackErrorResponse $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }

        return response()->json([
            'success' => 'Message send with success',
        ], 200);
    }

    /**
     * @throws AuthenticationException
     * @throws SlackIsNotConfigured
     */
    public function searchMessages(SlackPaginationParametersRequest $request, string $query): JsonResponse
    {
        $credentials = $this->getApplicationSlackCredentials(loggedApplication());

        /** @var array $parameters */
        $parameters = $request->validated();

        return response()->json($this->client->searchMessages($credentials, $query, $parameters));
    }

    /**
     * @throws SlackIsNotConfigured
     */
    private function getApplicationSlackCredentials(Application $application): SlackCredentials
    {
        $slackService = $application->getWorkspaceService(OctoolsSlack::make());

        if (!$slackService || empty($slackService->config['token'])) {
            throw new SlackIsNotConfigured();
        }

        return new SlackCredentials($slackService->config['token']);
    }
}
