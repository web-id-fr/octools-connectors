<?php

declare(strict_types=1);

namespace Webid\OctoolsGryzzly\Http\Controllers;

use Webid\Octools\Facades\Octools;
use Webid\OctoolsGryzzly\Services\GryzzlyServiceDecorator;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Webid\Octools\Models\Application;
use Webid\Octools\Models\Member;
use Webid\OctoolsGryzzly\Api\Entities\GryzzlyCredentials;
use Webid\OctoolsGryzzly\Api\Exceptions\GryzzlyIsNotConfigured;
use Webid\OctoolsGryzzly\Http\Requests\GryzzlyPaginationParametersRequest;
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
    public function getCompanyEmployees(GryzzlyPaginationParametersRequest $request): JsonResponse
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
    public function getCompanyProjects(GryzzlyPaginationParametersRequest $request): JsonResponse
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
    public function getTasksByProjectsUUID(GryzzlyPaginationParametersRequest $request, string $uuid): JsonResponse
    {
        $credentials = $this->getApplicationGryzzlyCredentials(loggedApplication());

        /* @var array $parameters */
        $parameters = $request->validated();

        return response()->json($this->client->getTasksByProjects($credentials, $uuid, (array)$parameters));
    }

    /**
     * @throws AuthenticationException
     * @throws GryzzlyIsNotConfigured
     */
    public function getDeclarationsByEmployee(GryzzlyPaginationParametersRequest $request, Member $member): JsonResponse
    {
        $credentials = $this->getApplicationGryzzlyCredentials(loggedApplication());

        /** @var string $memberUuid */
        $memberUuid = $member->getUsernameForService(Octools::getServiceByKey('gryzzly'));

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
