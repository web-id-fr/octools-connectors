<?php

declare(strict_types=1);

namespace Webid\OctoolsGryzzly\Http\Controllers;

use Webid\Octools\Shared\CursorPaginator;
use Webid\OctoolsGryzzly\Http\Requests\DeclarationParametersRequest;
use Webid\OctoolsGryzzly\Services\GryzzlyServiceDecorator;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Webid\Octools\Models\Application;
use Webid\Octools\Models\Member;
use Webid\OctoolsGryzzly\Api\Entities\GryzzlyCredentials;
use Webid\OctoolsGryzzly\Api\Exceptions\GryzzlyIsNotConfigured;
use Webid\OctoolsGryzzly\Http\Requests\CursorPaginatedRequest;
use Webid\OctoolsGryzzly\OctoolsGryzzly;

class GryzzlyController
{
    public function __construct(
        protected GryzzlyServiceDecorator $client,
    ) {
    }

    /**
     * @throws AuthenticationException
     * @throws GryzzlyIsNotConfigured
     */
    public function getCompanyEmployees(CursorPaginatedRequest $request): JsonResponse
    {
        $credentials = $this->getApplicationGryzzlyCredentials(loggedApplication());

        /* @var array $parameters */
        $parameters = $request->validated();

        return response()->json($this->client->getEmployees($credentials, (array)$parameters));
    }

    /**
     * @throws AuthenticationException
     * @throws GryzzlyIsNotConfigured
     */
    public function getCompanyEmployeeByUUID(Member $member): JsonResponse
    {
        /** @var string $memberGryzzlyUuid */
        $memberGryzzlyUuid = $member->getUsernameForService(OctoolsGryzzly::make());
        $credentials = $this->getApplicationGryzzlyCredentials(loggedApplication());
        return response()->json($this->client->getEmployeeByUuid($credentials, $memberGryzzlyUuid));
    }

    /**
     * @throws AuthenticationException
     * @throws GryzzlyIsNotConfigured
     */
    public function getCompanyProjects(CursorPaginatedRequest $request): JsonResponse
    {
        $credentials = $this->getApplicationGryzzlyCredentials(loggedApplication());

        /* @var array $parameters */
        $parameters = $request->validated();

        return response()->json($this->client->getProjects($credentials, (array)$parameters));
    }

    /**
     * @throws AuthenticationException
     * @throws GryzzlyIsNotConfigured
     */
    public function getTasksByProjectsUUID(CursorPaginatedRequest $request, string $uuid): JsonResponse
    {
        $credentials = $this->getApplicationGryzzlyCredentials(loggedApplication());

        /* @var array $parameters */
        $parameters = $request->validated();

        return response()->json($this->client->getTasksByProjects($credentials, $uuid, (array)$parameters));
    }

    public function getDeclarationsByEmployee(DeclarationParametersRequest $request, Member $member): JsonResponse
    {
        $credentials = $this->getApplicationGryzzlyCredentials(loggedApplication());

        /** @var string $memberUuid */
        $memberUuid = $member->getUsernameForService(OctoolsGryzzly::make());

        if (!$memberUuid) {
            return response()->json(new CursorPaginator(30, []));
        }

        /* @var array $parameters */
        $parameters = $request->validated();

        return response()->json(
            $this->client->getDeclarationsByEmployee(
                $credentials,
                $memberUuid,
                $parameters
            )
        );
    }

    /**
     * @throws GryzzlyIsNotConfigured
     */
    private function getApplicationGryzzlyCredentials(Application $application): GryzzlyCredentials
    {
        $gryzzlyService = $application->getWorkspaceService(OctoolsGryzzly::make());
        if (!$gryzzlyService || empty($gryzzlyService->config['token'])) {
            throw new GryzzlyIsNotConfigured();
        }

        return new GryzzlyCredentials($gryzzlyService->config['token']);
    }
}
