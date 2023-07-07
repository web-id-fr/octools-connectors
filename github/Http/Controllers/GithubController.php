<?php

declare(strict_types=1);

namespace Webid\OctoolsGithub\Http\Controllers;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Webid\OctoolsGithub\Api\Entities\GithubCredentials;
use Webid\OctoolsGithub\Api\Exceptions\CustomGithubMessageException;
use Webid\OctoolsGithub\Api\Exceptions\GithubIsNotConfigured;
use Webid\OctoolsGithub\Http\Requests\CursorPaginatedRequest;
use Webid\OctoolsGithub\Http\Requests\GithubPaginationParametersRequest;
use Webid\OctoolsGithub\OctoolsGithub;
use Webid\OctoolsGithub\Services\GithubServiceDecorator;
use Webid\Octools\Models\Application;
use Webid\Octools\Models\Member;

class GithubController
{
    public function __construct(
        private readonly GithubServiceDecorator $client,
    ) {
    }

    /**
     * @throws GithubIsNotConfigured
     * @throws AuthenticationException
     */
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
     * @throws GithubIsNotConfigured
     * @throws AuthenticationException
     */
    public function getCompanyRepositoryByName(string $repositoryName): JsonResponse
    {
        return response()->json($this->client->getCompanyRepositoryByName(
            $this->getApplicationGithubCredentials(loggedApplication()),
            $repositoryName,
        ));
    }

    /**
     * @throws GithubIsNotConfigured
     * @throws AuthenticationException
     */
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
     * @throws GithubIsNotConfigured
     * @throws AuthenticationException
     */
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
     * @throws GithubIsNotConfigured
     * @throws AuthenticationException
     */
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
     * @throws GithubIsNotConfigured
     * @throws AuthenticationException
     * @throws CustomGithubMessageException
     */
    public function getUserPullRequestsByRepository(
        string          $repositoryName,
        Member          $member,
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
     * @throws GithubIsNotConfigured
     * @throws AuthenticationException
     */
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
     * @throws GithubIsNotConfigured
     * @throws AuthenticationException
     */
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
     * @throws GithubIsNotConfigured
     * @throws AuthenticationException
     */
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

    public function restGenericEndpoint(Request $request, string $uri): JsonResponse
    {
        $parameters = $request->all();

        return response()->json(
            $this->client->restGenericEndpoint(
                $this->getApplicationGithubCredentials(loggedApplication()),
                $uri,
                $parameters
            )
        );
    }

    public function graphqlGenericEndpoint(Request $request): JsonResponse
    {
        $parameters = $request->all();

        return response()->json(
            $this->client->graphqlGenericEndpoint(
                $this->getApplicationGithubCredentials(loggedApplication()),
                $parameters
            )
        );
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
