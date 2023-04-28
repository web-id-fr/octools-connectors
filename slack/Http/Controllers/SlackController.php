<?php

declare(strict_types=1);

namespace Webid\OctoolsSlack\Http\Controllers;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use JoliCode\Slack\Exception\SlackErrorResponse;
use Webid\Octools\Models\Application;
use Webid\Octools\OpenApi\Responses\ErrorUnauthenticatedResponse;
use Webid\Octools\OpenApi\Responses\ErrorUnauthorizedResponse;
use Webid\OctoolsSlack\Api\Entities\SlackCredentials;
use Webid\OctoolsSlack\Api\Exceptions\SlackIsNotConfigured;
use Webid\OctoolsSlack\Http\Requests\SendSlackMessageRequest;
use Webid\OctoolsSlack\Http\Requests\SlackPaginationParametersRequest;
use Webid\OctoolsSlack\OctoolsSlack;
use Webid\OctoolsSlack\OpenApi\Parameters\CursorPaginatedParameters;
use Webid\OctoolsSlack\OpenApi\Parameters\SearchMessagesParameters;
use Webid\OctoolsSlack\OpenApi\Parameters\SendSlackMessageParameters;
use Webid\OctoolsSlack\OpenApi\Responses\ErrorSlackIsNotConfiguredResponse;
use Webid\OctoolsSlack\OpenApi\Responses\ListEmployeesResponse;
use Webid\OctoolsSlack\OpenApi\Responses\ListMessagesResponse;
use Webid\OctoolsSlack\OpenApi\Responses\MessageSuccessfullySentResponse;
use Webid\OctoolsSlack\Services\SlackServiceDecorator;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class SlackController
{
    public function __construct(
        private readonly SlackServiceDecorator $client,
    ) {
    }

    /**
     * Get company employees.
     *
     * @throws AuthenticationException
     * @throws SlackIsNotConfigured
     */
    #[OpenApi\Operation(tags: ['Slack'])]
    #[OpenApi\Parameters(factory: CursorPaginatedParameters::class)]
    #[OpenApi\Response(factory: ListEmployeesResponse::class)]
    #[OpenApi\Response(factory: ErrorUnauthorizedResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorSlackIsNotConfiguredResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorUnauthenticatedResponse::class, statusCode: 403)]
    public function getCompanyEmployees(SlackPaginationParametersRequest $request): JsonResponse
    {
        $credentials = $this->getApplicationSlackCredentials(loggedApplication());

        /** @var array $parameters */
        $parameters = $request->validated();

        return response()->json($this->client->getEmployees($credentials, $parameters));
    }

    /**
     * Send message to a channel.
     *
     * @throws AuthenticationException
     * @throws SlackIsNotConfigured
     */
    #[OpenApi\Operation(tags: ['Slack'])]
    #[OpenApi\Parameters(factory: SendSlackMessageParameters::class)]
    #[OpenApi\Response(factory: MessageSuccessfullySentResponse::class)]
    #[OpenApi\Response(factory: ErrorUnauthorizedResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorSlackIsNotConfiguredResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorUnauthenticatedResponse::class, statusCode: 403)]
    public function sendMessageToChannel(SendSlackMessageRequest $request): JsonResponse
    {
        /** @var string $message */
        $message = $request->validated('message');

        /** @var string $channel */
        $channel = $request->validated('channel');

        try {
            $credentials = $this->getApplicationSlackCredentials(loggedApplication());
            $this->client->sendMessageToChannel($credentials, $message, $channel);
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
     * Search messages.
     *
     * @throws AuthenticationException
     * @throws SlackIsNotConfigured
     */
    #[OpenApi\Operation(tags: ['Slack'])]
    #[OpenApi\Parameters(factory: SearchMessagesParameters::class)]
    #[OpenApi\Response(factory: ListMessagesResponse::class)]
    #[OpenApi\Response(factory: ErrorUnauthorizedResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorSlackIsNotConfiguredResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorUnauthenticatedResponse::class, statusCode: 403)]
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
