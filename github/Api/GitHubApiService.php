<?php

declare(strict_types=1);

namespace Webid\OctoolsGithub\Api;

use Illuminate\Config\Repository;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Webid\OctoolsGithub\Api\Entities\GithubCredentials;
use Webid\OctoolsGithub\Api\Entities\Issue;
use Webid\OctoolsGithub\Api\Entities\PullRequest;
use Webid\OctoolsGithub\Api\Entities\Repository as RepositoryDto;
use Webid\OctoolsGithub\Api\Entities\User;
use Webid\OctoolsGithub\Api\Exceptions\GithubInvalidJsonStructure;
use Webid\OctoolsGithub\Api\Exceptions\GithubQueryErrorException;
use Webid\OctoolsGithub\Api\Exceptions\RepositoryNotFoundException;
use Webid\Octools\Shared\CursorPaginator;
use Webmozart\Assert\Assert;

class GitHubApiService implements GitHubApiServiceInterface
{
    private const PER_PAGE = 30;

    public function __construct(
        private readonly Repository $config,
        private readonly Factory $http,
    ) {
    }

    /**
     * @throws \Webid\OctoolsGithub\Api\Exceptions\GithubInvalidJsonStructure
     * @throws \Webid\OctoolsGithub\Api\Exceptions\GithubQueryErrorException
     */
    public function getCompanyRepositories(
        GithubCredentials $credentials,
        array $paginationParams
    ): CursorPaginator {
        $paginationStr = $this->getPaginationStr($paginationParams);
        $orderByStr = $this->orderByStr($paginationParams);

        $repoAttributes = RepositoryDto::graphAttributes();

        $query = <<<GQL
            query {
              organization (login:"{$credentials->organization}") {
                repositories ({$paginationStr}, {$orderByStr}) {
                  totalCount,
                  nodes {
                    $repoAttributes
                  },
                  pageInfo { endCursor hasNextPage }
                }
              }
            }
            GQL;

        $json = $this->requestPaginate($credentials, $query, 'data.organization.repositories');

        return $this->cursorPaginatorFromResponse(
            $json,
            fn (array $item) => RepositoryDto::fromGraphArray($item),
        );
    }

    /**
     * @throws RepositoryNotFoundException
     */
    public function getCompanyRepositoryByName(
        GithubCredentials $credentials,
        string $repositoryName
    ): RepositoryDto {

        $repoAttributes = RepositoryDto::graphAttributes();

        $query=<<<GQL
            query {
              repository (owner: "{$credentials->organization}", name: "{$repositoryName}") {
                $repoAttributes
              }
            }
            GQL;

        /** @var Response $response */
        $response = $this->auth($credentials)
            ->post('graphql', ['query' => $query]);

        /** @var ?array $repo */
        $repo = $response->json('data.repository');

        if (empty($repo)) {
            throw new RepositoryNotFoundException();
        }

        return RepositoryDto::fromGraphArray($repo);
    }


    /**
     * @throws GithubInvalidJsonStructure
     * @throws GithubQueryErrorException
     * @throws RepositoryNotFoundException
     */
    public function getCompanyRepositoryIssues(
        GithubCredentials $credentials,
        string $repositoryName,
        array $paginationParams
    ): CursorPaginator {
        $paginationStr = $this->getPaginationStr($paginationParams);
        $orderByStr = $this->orderByStr($paginationParams);

        $issueAttributes = Issue::graphAttributes();

        $query=<<<GQL
            query {
              repository (owner: "{$credentials->organization}", name: "{$repositoryName}") {
                issues ({$paginationStr}, {$orderByStr}) {
                    totalCount,
                    nodes {
                      $issueAttributes
                    },
                    pageInfo { endCursor hasNextPage}
                }
              }
            }
            GQL;

        /** @var Response $response */
        $response = $this->auth($credentials)
            ->post('graphql', ['query' => $query]);

        /** @var ?array $repository */
        $repository = $response->json('data.repository');
        if (is_null($repository)) {
            throw new RepositoryNotFoundException();
        }

        $this->assertNoErrorsInResponse($response);

        /** @var ?array $json */
        $json = $repository['issues'] ?? null;
        $this->assertJsonPaginatedStructure($json);

        return $this->cursorPaginatorFromResponse(
            (array) $json,
            fn (array $item) => Issue::fromGraphArray($item)
        );
    }

    /**
     * @throws GithubInvalidJsonStructure
     * @throws GithubQueryErrorException
     */
    public function getCompanyEmployees(
        GithubCredentials $credentials,
        array $paginationParams
    ): CursorPaginator {
        $paginationStr = $this->getPaginationStr($paginationParams);

        $userAttributes = User::graphAttributes();

        $query = <<<GQL
            query {
              organization (login: "{$credentials->organization}") {
                membersWithRole ({$paginationStr}) {
                    totalCount,
                    nodes { $userAttributes }
                    pageInfo { endCursor hasNextPage }
                }
              }
            }
            GQL;

        return $this->cursorPaginatorFromResponse(
            $this->requestPaginate($credentials, $query, 'data.organization.membersWithRole'),
            fn (array $item) => User::fromGraphArray($item)
        );
    }

    /**
     * @throws GithubInvalidJsonStructure
     * @throws GithubQueryErrorException
     * @throws RepositoryNotFoundException
     */
    public function getRepositoryPullRequests(
        GithubCredentials $credentials,
        string $repositoryName,
        array $paginationParams
    ): CursorPaginator {
        $paginationStr = $this->getPaginationStr($paginationParams);
        $orderByStr = $this->orderByStr($paginationParams);

        $stateStr = null;
        if (!empty($paginationParams['state'])) {
            $stateStr = match ($paginationParams['state']) {
                'open' => 'states:[OPEN]',
                'closed' => 'states:[MERGED, CLOSED]',
                default => null
            };
        }

        $filterStr = join(', ', array_filter([$stateStr, $paginationStr, $orderByStr]));

        $prAttibutes = PullRequest::graphAttributes();

        $query = <<<GQL
            query {
              repository (owner: "{$credentials->organization}", name: "{$repositoryName}") {
                pullRequests ({$filterStr}) {
                  totalCount,
                  nodes {
                    $prAttibutes
                  },
                  pageInfo { endCursor hasNextPage }
                }
              }
            }
            GQL;

        /** @var Response $response */
        $response = $this->auth($credentials)
            ->post('graphql', ['query' => $query]);

        /** @var ?array $repository */
        $repository = $response->json('data.repository');

        if (is_null($repository)) {
            throw new RepositoryNotFoundException();
        }

        $this->assertNoErrorsInResponse($response);

        /** @var ?array $json */
        $json = $repository['pullRequests'] ?? null;
        $this->assertJsonPaginatedStructure($json);

        return $this->cursorPaginatorFromResponse(
            (array) $json,
            fn (array $item) => PullRequest::fromGraphArray($item)
        );
    }

    /**
     * @throws GithubInvalidJsonStructure
     * @throws GithubQueryErrorException
     */
    public function getUserPullRequestsByRepository(
        GithubCredentials $credentials,
        string $username,
        string $repositoryName,
        array $paginationParams
    ): CursorPaginator {
        $paginationStr = $this->getPaginationStr($paginationParams);

        $countKey = 'issueCount';

        $prAttributes = PullRequest::graphAttributes();

        $filterStr = "repo:{$credentials->organization}/{$repositoryName} " .
            "is:pr is:open ".
            "author:{$username}";
        //review-requested:{$username} | assignee:{$username}

        $query=<<<GQL
            query {
              search(query: "$filterStr", type: ISSUE, {$paginationStr}) {
                  {$countKey}
                  nodes {
                    ... on PullRequest {
                      $prAttributes
                    }
                  }
                  pageInfo { endCursor hasNextPage }
              }
            }
            GQL;

        return $this->cursorPaginatorFromResponse(
            $this->requestPaginate($credentials, $query, 'data.search', $countKey),
            fn (array $item) => PullRequest::fromGraphArray($item),
            $countKey
        );
    }

    /**
     * @throws GithubInvalidJsonStructure
     * @throws GithubQueryErrorException
     */
    public function searchRepositories(
        GithubCredentials $credentials,
        string $search,
        array $paginationParams
    ): CursorPaginator {
        $paginationStr = $this->getPaginationStr($paginationParams);

        $repoAttributes = RepositoryDto::graphAttributes();

        $countKey = 'repositoryCount';

        $query =<<<GQL
            query {
              search(query: "{$search} org:{$credentials->organization}", type: REPOSITORY, {$paginationStr}) {
                {$countKey}
                 nodes {
                    ... on Repository {
                      $repoAttributes
                    }
                },
                pageInfo { endCursor hasNextPage }
              }
            }
            GQL;

        return $this->cursorPaginatorFromResponse(
            $this->requestPaginate($credentials, $query, 'data.search', $countKey),
            fn (array $item) => RepositoryDto::fromGraphArray($item),
            $countKey
        );
    }

    /**
     * @throws GithubQueryErrorException
     * @throws GithubInvalidJsonStructure
     */
    public function searchIssues(
        GithubCredentials $credentials,
        string $search,
        array $paginationParams
    ): CursorPaginator {
        $paginationStr = $this->getPaginationStr($paginationParams);
        $issueAttributes = Issue::graphAttributes();

        $filter = "{$search} type:issue org:{$credentials->organization}";

        $countKey = 'issueCount';

        $query =<<<GQL
            query {
              search(query: "{$filter}", type: ISSUE, {$paginationStr}) {
                {$countKey}
                nodes {
                  ... on Issue {
                    $issueAttributes
                  }
                },
                pageInfo {endCursor hasNextPage}
              }
            }
            GQL;

        return $this->cursorPaginatorFromResponse(
            $this->requestPaginate($credentials, $query, 'data.search', $countKey),
            fn (array $item) => Issue::fromGraphArray($item),
            $countKey
        );
    }

    /**
     * @throws GithubInvalidJsonStructure
     * @throws GithubQueryErrorException
     */
    public function searchPullRequests(
        GithubCredentials $credentials,
        string $search,
        array $paginationParams
    ): CursorPaginator {
        $paginationStr = $this->getPaginationStr($paginationParams);
        $prAttributes = PullRequest::graphAttributes();

        $filter = "{$search} is:pr is:open in:title org:{$credentials->organization}";

        $countKey = 'issueCount';

        $query =<<<GQL
            query {
              search(query: "{$filter}", type: ISSUE, {$paginationStr}) {
                {$countKey}
                nodes {
                  ... on PullRequest {
                    $prAttributes
                  }
                },
                pageInfo { endCursor hasNextPage }
              }
            }
            GQL;

        return $this->cursorPaginatorFromResponse(
            $this->requestPaginate($credentials, $query, 'data.search', $countKey),
            fn (array $item) => PullRequest::fromGraphArray($item),
            $countKey
        );
    }

    private function auth(GithubCredentials $credentials): PendingRequest
    {
        /** @var string $url */
        $url = $this->config->get('octools-github.url');

        return $this->http
            ->withToken($credentials->token)
            ->throw()
            ->asJson()
            ->baseUrl($url);
    }

    /**
     * @throws GithubInvalidJsonStructure
     * @throws GithubQueryErrorException
     */
    private function requestPaginate(
        GithubCredentials $credentials,
        string $query,
        string $responseKey,
        string $countKey = 'totalCount'
    ): array {
        /** @var Response $response */
        $response = $this->auth($credentials)
            ->post('graphql', ['query' => $query]);

        $this->assertNoErrorsInResponse($response);

        /** @var ?array $json */
        $json = $response->json($responseKey);
        $this->assertJsonPaginatedStructure($json, $countKey);

        return (array)$json;
    }

    /**
     * @param array $parameters
     * @return string
     */
    public function getPaginationStr(array $parameters): string
    {
        $perPage = self::PER_PAGE;
        $cursor = $parameters['cursor'] ?? null;

        return "first: {$perPage}," . (!empty($cursor) ? " after:\"{$cursor}\"" : '');
    }

    private function orderByStr(array $parameters): string
    {
        $allowedSortFields = ['created_at', 'updated_at'];
        $allowedDirections = ['asc', 'desc'];

        $sort = 'updated_at';
        if (isset($parameters['sort']) && in_array($parameters['sort'], $allowedSortFields)) {
            $sort = $parameters['sort'];
        }

        $direction = 'desc';
        if (isset($parameters['direction']) && in_array($parameters['direction'], $allowedDirections)) {
            $direction = $parameters['direction'];
        }

        $params = [
            'sort' => strtoupper($sort),
            'direction' => strtoupper($direction),
        ];

        return "orderBy: { field: {$params['sort']}, direction: {$params['direction']} }";
    }

    private function cursorPaginatorFromResponse(
        array $json,
        callable $nodesCallback,
        string $countKey = 'totalCount'
    ): CursorPaginator {
        return new CursorPaginator(
            self::PER_PAGE,
            array_map(
                fn(array $item) => $nodesCallback($item),
                $json['nodes']
            ),
            $json[$countKey],
            ($json['pageInfo']['hasNextPage'] ?? false) ? ($json['pageInfo']['endCursor'] ?? null) : null,
        );
    }

    /**
     * @throws GithubInvalidJsonStructure
     */
    private function assertJsonPaginatedStructure(?array $json, string $countKey = 'totalCount'): void
    {
        try {
            Assert::isMap($json);
            Assert::keyExists($json, $countKey);
            Assert::keyExists($json, 'nodes');
            Assert::isList($json['nodes']);
            Assert::keyExists($json, 'pageInfo');
            Assert::keyExists($json['pageInfo'], 'hasNextPage');
            Assert::keyExists($json['pageInfo'], 'endCursor');
        } catch (\InvalidArgumentException $e) {
            throw new GithubInvalidJsonStructure();
        }
    }

    /**
     * @throws GithubQueryErrorException
     */
    private function assertNoErrorsInResponse(Response $response): void
    {
        $errors = $response->json('errors');
        if (isset($errors) && is_array($errors) && !empty($errors)) {
            throw GithubQueryErrorException::fromErrorResponse($errors);
        }
    }
}
