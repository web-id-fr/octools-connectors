<?php

declare(strict_types=1);

namespace Webid\OctoolsGryzzly\Http\Controllers;

use Webid\Octools\OpenApi\Responses\ErrorUnauthenticatedResponse;
use Webid\Octools\OpenApi\Responses\ErrorUnauthorizedResponse;
use Webid\OctoolsGryzzly\OpenApi\Parameters\CompanyEmployeeByMemberIdParameters;
use Webid\OctoolsGryzzly\OpenApi\Parameters\CursorPaginatedParameters;
use Webid\OctoolsGryzzly\OpenApi\Parameters\DeclarationsByMemberIdParameters;
use Webid\OctoolsGryzzly\OpenApi\Parameters\TasksByProjectsUUIDParameters;
use Webid\OctoolsGryzzly\OpenApi\Responses\EmployeeResponse;
use Webid\OctoolsGryzzly\OpenApi\Responses\ErrorGryzzlyEmployeeNotFoundResponse;
use Webid\OctoolsGryzzly\OpenApi\Responses\ErrorGryzzlyIsNotConfiguredResponse;
use Webid\OctoolsGryzzly\OpenApi\Responses\ListDeclarationsResponse;
use Webid\OctoolsGryzzly\OpenApi\Responses\ListEmployeesResponse;
use Webid\OctoolsGryzzly\OpenApi\Responses\ListProjectsResponse;
use Webid\OctoolsGryzzly\OpenApi\Responses\ListTasksResponse;
use Webid\OctoolsGryzzly\Services\GryzzlyServiceDecorator;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Webid\Octools\Models\Application;
use Webid\Octools\Models\Member;
use Webid\OctoolsGryzzly\Api\Entities\GryzzlyCredentials;
use Webid\OctoolsGryzzly\Api\Exceptions\GryzzlyIsNotConfigured;
use Webid\OctoolsGryzzly\Http\Requests\GryzzlyPaginationParametersRequest;
use Webid\OctoolsGryzzly\OctoolsGryzzly;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class GryzzlyController
{
    public function __construct(
        protected GryzzlyServiceDecorator $client,
    ) {
    }

    /**
     * Get company employees.
     *
     * @throws AuthenticationException
     * @throws GryzzlyIsNotConfigured
     */
    #[OpenApi\Operation(id: 'getGryzzlyCompanyEmployees', tags: ['Gryzzly'])]
    #[OpenApi\Parameters(factory: CursorPaginatedParameters::class)]
    #[OpenApi\Response(factory: ListEmployeesResponse::class)]
    #[OpenApi\Response(factory: ErrorUnauthorizedResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorGryzzlyIsNotConfiguredResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorUnauthenticatedResponse::class, statusCode: 403)]
    public function getCompanyEmployees(GryzzlyPaginationParametersRequest $request): JsonResponse
    {
        $credentials = $this->getApplicationGryzzlyCredentials(loggedApplication());

        /* @var array $parameters */
        $parameters = $request->validated();

        return response()->json($this->client->getEmployees($credentials, (array)$parameters));
    }

    /**
     * Get company employee by Octools Member ID.
     *
     * @throws AuthenticationException
     * @throws GryzzlyIsNotConfigured
     */
    #[OpenApi\Operation(id: 'getGryzzlyCompanyEmployeeByUUID', tags: ['Gryzzly'])]
    #[OpenApi\Parameters(factory: CompanyEmployeeByMemberIdParameters::class)]
    #[OpenApi\Response(factory: EmployeeResponse::class)]
    #[OpenApi\Response(factory: ErrorUnauthorizedResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorGryzzlyIsNotConfiguredResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorUnauthenticatedResponse::class, statusCode: 403)]
    #[OpenApi\Response(factory: ErrorGryzzlyEmployeeNotFoundResponse::class, statusCode: 404)]
    public function getCompanyEmployeeByUUID(Member $member): JsonResponse
    {
        /** @var string $memberGryzzlyUuid */
        $memberGryzzlyUuid = $member->getUsernameForService(OctoolsGryzzly::make());

        $credentials = $this->getApplicationGryzzlyCredentials(loggedApplication());
        return response()->json($this->client->getEmployeeByUuid($credentials, $memberGryzzlyUuid));
    }

    /**
     * Get company projects.
     *
     * @throws AuthenticationException
     * @throws GryzzlyIsNotConfigured
     */
    #[OpenApi\Operation(id: 'getGryzzlyCompanyProjects', tags: ['Gryzzly'])]
    #[OpenApi\Parameters(factory: CursorPaginatedParameters::class)]
    #[OpenApi\Response(factory: ListProjectsResponse::class)]
    #[OpenApi\Response(factory: ErrorUnauthorizedResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorGryzzlyIsNotConfiguredResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorUnauthenticatedResponse::class, statusCode: 403)]
    public function getCompanyProjects(GryzzlyPaginationParametersRequest $request): JsonResponse
    {
        $credentials = $this->getApplicationGryzzlyCredentials(loggedApplication());

        /* @var array $parameters */
        $parameters = $request->validated();

        return response()->json($this->client->getProjects($credentials, (array)$parameters));
    }

    /**
     * Get tasks by project UUID.
     *
     * @throws AuthenticationException
     * @throws GryzzlyIsNotConfigured
     */
    #[OpenApi\Operation(id: 'getGryzzlyTasksByProjectsUUID', tags: ['Gryzzly'])]
    #[OpenApi\Parameters(factory: TasksByProjectsUUIDParameters::class)]
    #[OpenApi\Response(factory: ListTasksResponse::class)]
    #[OpenApi\Response(factory: ErrorUnauthorizedResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorGryzzlyIsNotConfiguredResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorUnauthenticatedResponse::class, statusCode: 403)]
    #[OpenApi\Response(factory: ErrorGryzzlyEmployeeNotFoundResponse::class, statusCode: 404)]
    public function getTasksByProjectsUUID(GryzzlyPaginationParametersRequest $request, string $uuid): JsonResponse
    {
        $credentials = $this->getApplicationGryzzlyCredentials(loggedApplication());

        /* @var array $parameters */
        $parameters = $request->validated();

        return response()->json($this->client->getTasksByProjects($credentials, $uuid, (array)$parameters));
    }

    /**
     * Get declarations by Octools Member ID.
     *
     * @throws AuthenticationException
     * @throws GryzzlyIsNotConfigured
     */
    #[OpenApi\Operation(id: 'getGryzzlyDeclarationsByEmployee', tags: ['Gryzzly'])]
    #[OpenApi\Parameters(factory: DeclarationsByMemberIdParameters::class)]
    #[OpenApi\Response(factory: ListDeclarationsResponse::class)]
    #[OpenApi\Response(factory: ErrorUnauthorizedResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorGryzzlyIsNotConfiguredResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorUnauthenticatedResponse::class, statusCode: 403)]
    #[OpenApi\Response(factory: ErrorGryzzlyEmployeeNotFoundResponse::class, statusCode: 404)]
    public function getDeclarationsByEmployee(GryzzlyPaginationParametersRequest $request, Member $member): JsonResponse
    {
        $credentials = $this->getApplicationGryzzlyCredentials(loggedApplication());

        /** @var string $memberUuid */
        $memberUuid = $member->getUsernameForService(OctoolsGryzzly::make());

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
