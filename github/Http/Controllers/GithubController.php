<?php

declare(strict_types=1);

namespace Webid\OctoolsGithub\Http\Controllers;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Webid\Octools\OpenApi\Responses\ErrorUnauthenticatedResponse;
use Webid\Octools\OpenApi\Responses\ErrorUnauthorizedResponse;
use Webid\OctoolsGithub\Api\Entities\GithubCredentials;
use Webid\OctoolsGithub\Api\Exceptions\CustomGithubMessageException;
use Webid\OctoolsGithub\Api\Exceptions\GithubIsNotConfigured;
use Webid\OctoolsGithub\Http\Requests\CursorPaginatedRequest;
use Webid\OctoolsGithub\Http\Requests\GithubPaginationParametersRequest;
use Webid\OctoolsGithub\OctoolsGithub;
use Webid\OctoolsGithub\OpenApi\Parameters\ListCompanyEmployeesParameters;
use Webid\OctoolsGithub\OpenApi\Parameters\ListCompanyRepositoryIssuesParameters;
use Webid\OctoolsGithub\OpenApi\Parameters\ListRepositoriesPullRequestsParameters;
use Webid\OctoolsGithub\OpenApi\Parameters\QuerySearchPaginationParameters;
use Webid\OctoolsGithub\OpenApi\Parameters\ListRepositoriesUserPullRequestsParameters;
use Webid\OctoolsGithub\OpenApi\Responses\ErrorGithubIsNotConfiguredResponse;
use Webid\OctoolsGithub\OpenApi\Responses\ErrorGithubMemberDoesNotHaveGithubUsernameResponse;
use Webid\OctoolsGithub\OpenApi\Responses\ErrorGithubRepositoryNotFoundResponse;
use Webid\OctoolsGithub\OpenApi\Responses\ListEmployeesResponse;
use Webid\OctoolsGithub\OpenApi\Responses\ListIssuesResponse;
use Webid\OctoolsGithub\OpenApi\Responses\ListPullRequestsResponse;
use Webid\OctoolsGithub\OpenApi\Responses\ListRepositoriesResponse;
use Webid\OctoolsGithub\OpenApi\Responses\RepositoryResponse;
use Webid\OctoolsGithub\Services\GithubServiceDecorator;
use Webid\Octools\Models\Application;
use Webid\Octools\Models\Member;
use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;

#[OpenApi\PathItem]
class GithubController
{
    public function __construct(
        private readonly GithubServiceDecorator $client,
    ) {
    }

    /**
     * Get company repositories.
     *
     * @throws GithubIsNotConfigured
     * @throws AuthenticationException
     */
    #[OpenApi\Operation(id: 'getCompanyRepositories', tags: ['Github'])]
    #[OpenApi\Response(factory: ListRepositoriesResponse::class)]
    #[OpenApi\Response(factory: ErrorUnauthorizedResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorGithubIsNotConfiguredResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorUnauthenticatedResponse::class, statusCode: 403)]
    public function getCompanyRepositories(GithubPaginationParametersRequest $request): JsonResponse
    {
        /** @var array $parameters */
        $parameters = $request->validated();

        return response()->json($this->client->getCompanyRepositories(
            $this->getApplicationGithubCredentials(loggedApplication()),
            $parameters
        ));
    }

    /**
     * Get company repository by name.
     *
     * @param string $repositoryName Repository name
     *
     * @throws GithubIsNotConfigured
     * @throws AuthenticationException
     */
    #[OpenApi\Operation(id: 'getCompanyRepositoryByName', tags: ['Github'])]
    #[OpenApi\Response(factory: RepositoryResponse::class)]
    #[OpenApi\Response(factory: ErrorUnauthorizedResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorGithubIsNotConfiguredResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorUnauthenticatedResponse::class, statusCode: 403)]
    public function getCompanyRepositoryByName(string $repositoryName): JsonResponse
    {
        return response()->json($this->client->getCompanyRepositoryByName(
            $this->getApplicationGithubCredentials(loggedApplication()),
            $repositoryName,
        ));
    }

    /**
     * Get company repository issues.
     *
     * @param string $repositoryName Repository name
     *
     * @throws GithubIsNotConfigured
     * @throws AuthenticationException
     */
    #[OpenApi\Operation(id: 'getCompanyRepositoryIssues', tags: ['Github'])]
    #[OpenApi\Parameters(factory: ListCompanyRepositoryIssuesParameters::class)]
    #[OpenApi\Response(factory: ListIssuesResponse::class)]
    #[OpenApi\Response(factory: ErrorUnauthorizedResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorGithubIsNotConfiguredResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorUnauthenticatedResponse::class, statusCode: 403)]
    #[OpenApi\Response(factory: ErrorGithubRepositoryNotFoundResponse::class, statusCode: 404)]
    public function getCompanyRepositoryIssues(GithubPaginationParametersRequest $request, string $repositoryName) : JsonResponse
    {
        /** @var array $parameters */
        $parameters = $request->validated();

        return response()->json($this->client->getCompanyRepositoryIssues(
            $this->getApplicationGithubCredentials(loggedApplication()),
            $repositoryName,
            $parameters
        ));
    }

    /**
     * Get company employees.
     *
     * @throws GithubIsNotConfigured
     * @throws AuthenticationException
     */
    #[OpenApi\Operation(id: 'getCompanyEmployees', tags: ['Github'])]
    #[OpenApi\Parameters(factory: ListCompanyEmployeesParameters::class)]
    #[OpenApi\Response(factory: ListEmployeesResponse::class)]
    #[OpenApi\Response(factory: ErrorUnauthorizedResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorGithubIsNotConfiguredResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorUnauthenticatedResponse::class, statusCode: 403)]
    #[OpenApi\Response(factory: ErrorGithubRepositoryNotFoundResponse::class, statusCode: 404)]
    public function getCompanyEmployees(GithubPaginationParametersRequest $request): JsonResponse
    {
        /** @var array $parameters */
        $parameters = $request->validated();

        return response()->json($this->client->getCompanyEmployees(
            $this->getApplicationGithubCredentials(loggedApplication()),
            $parameters
        ));
    }

    /**
     * Get repository pull requests.
     *
     * @throws GithubIsNotConfigured
     * @throws AuthenticationException
     */
    #[OpenApi\Operation(id: 'getRepositoryPullrequests', tags: ['Github'])]
    #[OpenApi\Parameters(factory: ListRepositoriesPullRequestsParameters::class)]
    #[OpenApi\Response(factory: ListPullRequestsResponse::class)]
    #[OpenApi\Response(factory: ErrorUnauthorizedResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorGithubIsNotConfiguredResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorUnauthenticatedResponse::class, statusCode: 403)]
    #[OpenApi\Response(factory: ErrorGithubRepositoryNotFoundResponse::class, statusCode: 404)]
    public function getRepositoryPullRequests(GithubPaginationParametersRequest $request, string $repositoryName): JsonResponse
    {
        /** @var array $parameters */
        $parameters = $request->validated();

        return response()->json(
            $this->client->getRepositoryPullRequests(
                $this->getApplicationGithubCredentials(loggedApplication()),
                $repositoryName,
                $parameters
            )
        );
    }

    /**
     * Get user pull requests by repository.
     *
     * @throws GithubIsNotConfigured
     * @throws AuthenticationException
     * @throws CustomGithubMessageException
     */
    #[OpenApi\Operation(id: 'getUserPullRequestsByRepository', tags: ['Github'])]
    #[OpenApi\Parameters(factory: ListRepositoriesUserPullRequestsParameters::class)]
    #[OpenApi\Response(factory: ListPullRequestsResponse::class)]
    #[OpenApi\Response(factory: ErrorUnauthorizedResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorGithubIsNotConfiguredResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorUnauthenticatedResponse::class, statusCode: 403)]
    #[OpenApi\Response(factory: ErrorGithubRepositoryNotFoundResponse::class, statusCode: 404)]
    #[OpenApi\Response(factory: ErrorGithubMemberDoesNotHaveGithubUsernameResponse::class, statusCode: 404)]
    public function getUserPullRequestsByRepository(
        string $repositoryName,
        Member $member,
        CursorPaginatedRequest $request
    ): JsonResponse {
        /** @var string $username */
        $username = $member->getUsernameForService(OctoolsGithub::make());

        if (empty($username)) {
            throw new CustomGithubMessageException('Member does not have github username.');
        }

        /** @var array $parameters */
        $parameters = $request->validated();

        return response()->json($this->client->getUserPullRequestsByRepository(
            $this->getApplicationGithubCredentials(loggedApplication()),
            $username,
            $repositoryName,
            $parameters
        ));
    }

    /**
     * Search repositories.
     *
     * @throws GithubIsNotConfigured
     * @throws AuthenticationException
     */
    #[OpenApi\Operation(id: 'searchRepositories', tags: ['Github'])]
    #[OpenApi\Parameters(factory: QuerySearchPaginationParameters::class)]
    #[OpenApi\Response(factory: ListRepositoriesResponse::class)]
    #[OpenApi\Response(factory: ErrorUnauthorizedResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorGithubIsNotConfiguredResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorUnauthenticatedResponse::class, statusCode: 403)]
    public function searchRepositories(CursorPaginatedRequest $request, string $query): JsonResponse
    {
        /** @var array $parameters */
        $parameters = $request->validated();

        return response()->json($this->client->searchRepositories(
            $this->getApplicationGithubCredentials(loggedApplication()),
            $query,
            $parameters
        ));
    }

    /**
     * Search issues.
     *
     * @throws GithubIsNotConfigured
     * @throws AuthenticationException
     */
    #[OpenApi\Operation(id: 'searchIssues', tags: ['Github'])]
    #[OpenApi\Parameters(factory: QuerySearchPaginationParameters::class)]
    #[OpenApi\Response(factory: ListIssuesResponse::class)]
    #[OpenApi\Response(factory: ErrorUnauthorizedResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorGithubIsNotConfiguredResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorUnauthenticatedResponse::class, statusCode: 403)]
    public function searchIssues(CursorPaginatedRequest $request, string $query): JsonResponse
    {
        /** @var array $parameters */
        $parameters = $request->validated();

        return response()->json($this->client->searchIssues(
            $this->getApplicationGithubCredentials(loggedApplication()),
            $query,
            $parameters
        ));
    }

    /**
     * Search pull requests.
     *
     * @throws GithubIsNotConfigured
     * @throws AuthenticationException
     */
    #[OpenApi\Operation(id: 'searchPullRequests', tags: ['Github'])]
    #[OpenApi\Parameters(factory: QuerySearchPaginationParameters::class)]
    #[OpenApi\Response(factory: ListIssuesResponse::class)]
    #[OpenApi\Response(factory: ErrorUnauthorizedResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorGithubIsNotConfiguredResponse::class, statusCode: 401)]
    #[OpenApi\Response(factory: ErrorUnauthenticatedResponse::class, statusCode: 403)]
    public function searchPullRequests(CursorPaginatedRequest $request, string $query): JsonResponse
    {
        /** @var array $parameters */
        $parameters = $request->validated();

        return response()->json($this->client->searchPullRequests(
            $this->getApplicationGithubCredentials(loggedApplication()),
            $query,
            $parameters
        ));
    }

    /**
     * @throws GithubIsNotConfigured
     */
    private function getApplicationGithubCredentials(Application $application): GithubCredentials
    {
        $githubService = $application->getWorkspaceService(OctoolsGithub::make());

        if (!$githubService || empty($githubService->config['token']) || empty($githubService->config['organization'])) {
            throw new GithubIsNotConfigured();
        }

        return new GithubCredentials(
            $githubService->config['organization'],
            $githubService->config['token']
        );
    }
}
