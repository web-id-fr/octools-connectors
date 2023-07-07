<?php

declare(strict_types=1);

namespace Webid\OctoolsGithub\Api;

use Webid\OctoolsGithub\Api\Entities\GithubCredentials;
use Webid\OctoolsGithub\Api\Entities\Issue;
use Webid\OctoolsGithub\Api\Entities\PullRequest;
use Webid\OctoolsGithub\Api\Entities\Repository;
use Webid\OctoolsGithub\Api\Entities\User;
use Webid\Octools\Shared\CursorPaginator;

class FakeGitHubApiService implements GitHubApiServiceInterface
{

    public function getCompanyRepositories(
        GithubCredentials $credentials,
        array $paginationParams
    ): CursorPaginator {
        return new CursorPaginator(
            10,
            [
                $this->createWebIdRepository(),
                $this->createOctoolsRepository(),
                $this->createNovaTabsMultipleRelation(),
            ],
            3
        );
    }

    public function getCompanyRepositoryByName(
        GithubCredentials $credentials,
        string $repositoryName
    ): Repository {
        return $this->createOctoolsRepository();
    }

    public function getCompanyRepositoryIssues(
        GithubCredentials $credentials,
        string $repositoryName,
        array $paginationParams
    ): CursorPaginator {
        return new CursorPaginator(
            10,
            [
                Issue::fromGraphArray([
                    "title" => "[CONFIGURATEUR] Erreur 404",
                    "number" => 253,
                    "state" => "OPEN",
                    "url" => "https://github.com/web-id-fr/carbon-clap/issues/253",
                    "updatedAt" => "2023-02-01T13:29:20Z"
                ]),
                Issue::fromGraphArray([
                    "title" => "[BILAN - COMPARER] Corriger légende des graphiques",
                    "number" => 250,
                    "state" => "OPEN",
                    "url" => "https://github.com/web-id-fr/carbon-clap/issues/250",
                    "updatedAt" => "2023-02-01T12:47:36Z"
                ]),
                Issue::fromGraphArray([
                    "title" => "[Moyen Tech de Prod] Bug Répétable Moyen Tech Spéciaux",
                    "number" => 185,
                    "state" => "OPEN",
                    "url" => "https://github.com/web-id-fr/carbon-clap/issues/185",
                    "updatedAt" => "2023-02-01T12:35:35Z"
                ]),
            ],
            3,
        );
    }

    public function getCompanyEmployees(
        GithubCredentials $credentials,
        array $paginationParams
    ): CursorPaginator {
        return new CursorPaginator(
            10,
            [
                new User(
                    'jelore',
                    'Jonathan ELORE',
                    'jonathan@web-id.fr',
                    'https://avatars.githubusercontent.com/u/5576976?v=4'
                ),
                new User(
                    'CLEMREP',
                    'Clement REPEL',
                    'clement@web-id.fr',
                    'https://avatars.githubusercontent.com/u/62845501?v=4'
                ),
                new User(
                    'cedric-webid',
                    'Cedric Bidet',
                    'cedric@web-id.fr',
                    'https://avatars.githubusercontent.com/u/44647081?v=4'
                ),
            ],
            3
        );
    }

    public function getRepositoryPullRequests(
        GithubCredentials $credentials,
        string $repositoryName,
        array $paginationParams
    ): CursorPaginator {
        return new CursorPaginator(
            10,
            [
                PullRequest::fromGraphArray([
                    "title" => ":pencil2: Update wording for \"Ne sais pas\"",
                    "number" => 19,
                    "url" => "https://github.com/web-id-fr/carbon-clap/pull/19",
                    "state" => "OPEN",
                    "author" => [
                        "login" => "elise-web-id"
                    ],
                    "closingIssuesReferences" => [
                        "nodes" => [
                            [
                                "title" => "[GENERAL] Changement wording option select",
                                "number" => 13,
                                "state" => "OPEN",
                                "url" => "https://github.com/web-id-fr/carbon-clap/issues/13",
                                "updatedAt" => "2022-12-01T13:29:20Z"
                            ]
                        ]
                    ],
                    "reviewRequests" => [
                        "nodes" => []
                    ],
                    "assignees" => [],
                ])
            ],
            1
        );
    }

    public function getUserPullRequestsByRepository(
        GithubCredentials $credentials,
        string $username,
        string $repositoryName,
        array $paginationParams
    ): CursorPaginator {
        return new CursorPaginator(
            10,
            [
                PullRequest::fromGraphArray([
                    "title" => ":pencil2: Update wording for \"Ne sais pas\"",
                    "number" => 19,
                    "url" => "https://github.com/web-id-fr/carbon-clap/pull/19",
                    "state" => "OPEN",
                    "author" => [
                        "login" => "elise-web-id"
                    ],
                    "closingIssuesReferences" => [
                        "nodes" => [
                            [
                                "title" => "[GENERAL] Changement wording option select",
                                "number" => 13,
                                "state" => "OPEN",
                                "url" => "https://github.com/web-id-fr/carbon-clap/issues/13",
                                "updatedAt" => "2022-12-01T13:29:20Z"
                            ]
                        ]
                    ],
                    "reviewRequests" => [
                        "nodes" => []
                    ],
                    "assignees" => [],
                ])
            ],
            1
        );
    }

    public function searchRepositories(
        GithubCredentials $credentials,
        string $search,
        array $paginationParams
    ): CursorPaginator {
        return new CursorPaginator(
            10,
            [
                $this->createWebIdRepository(),
                $this->createOctoolsRepository(),
                $this->createNovaTabsMultipleRelation(),
            ],
            3
        );
    }

    public function searchIssues(
        GithubCredentials $credentials,
        string $search,
        array $paginationParams
    ): CursorPaginator {
        return new CursorPaginator(
            10,
            [
                Issue::fromGraphArray([
                    "title" => "[CONFIGURATEUR] Erreur 404",
                    "number" => 253,
                    "state" => "OPEN",
                    "url" => "https://github.com/web-id-fr/carbon-clap/issues/253",
                    "updatedAt" => "2023-02-01T13:29:20Z"
                ]),
                Issue::fromGraphArray([
                    "title" => "[BILAN - COMPARER] Corriger légende des graphiques",
                    "number" => 250,
                    "state" => "OPEN",
                    "url" => "https://github.com/web-id-fr/carbon-clap/issues/250",
                    "updatedAt" => "2023-02-01T12:47:36Z"
                ]),
                Issue::fromGraphArray([
                    "title" => "[Moyen Tech de Prod] Bug Répétable Moyen Tech Spéciaux",
                    "number" => 185,
                    "state" => "OPEN",
                    "url" => "https://github.com/web-id-fr/carbon-clap/issues/185",
                    "updatedAt" => "2023-02-01T12:35:35Z"
                ]),
            ],
            3
        );
    }

    public function searchPullRequests(
        GithubCredentials $credentials,
        string $search,
        array $paginationParams
    ): CursorPaginator {
        return new CursorPaginator(
            10,
            [
                $this->createWebIdRepository(),
                $this->createOctoolsRepository(),
                $this->createNovaTabsMultipleRelation(),
            ],
            3
        );
    }

    private function createWebIdRepository(): Repository
    {
        return new Repository(
            1,
            'web-id-site',
            'Web^ID site web',
            'https://github.com/web-id-fr/web-id-site',
            false,
            'git@github.com:web-id-fr/web-id-site.git',
            '2022-12-01 21:22:23'
        );
    }

    private function createOctoolsRepository(): Repository
    {
        return new Repository(
            2,
            'octools-client',
            'Octools client',
            'https://github.com/web-id-fr/octools-client',
            false,
            'git@github.com:web-id-fr/octools-client.git',
            '2022-12-10 21:23:24'
        );
    }

    private function createNovaTabsMultipleRelation(): Repository
    {
        return new Repository(
            3,
            'nova-tabs-multiple-relation',
            'Laravel Nova Tabs Package',
            'https://github.com/web-id-fr/nova-tabs-multiple-relation',
            true,
            'git@github.com:web-id-fr/nova-tabs-multiple-relation.git',
            '2023-01-01 20:23:22'
        );
    }

    public function restGenericEndpoint(GithubCredentials $credentials, string $endpoint, array $parameters): array
    {
        return [
            "total_count" => 1,
            "incomplete_results" => false,
            "items" => [
                [
                    "title" => "[CONFIGURATEUR] Erreur 404",
                    "number" => 253,
                    "state" => "OPEN",
                    "url" => "",
                    "updatedAt" => "2023-02-01T13:29:20Z"
                ],
            ]
        ];
    }

    public function graphqlGenericEndpoint(GithubCredentials $credentials, array $parameters): array
    {
        return [
            "total_count" => 1,
            "incomplete_results" => false,
            "items" => [
                [
                    "title" => "[CONFIGURATEUR] Erreur 404",
                    "number" => 253,
                    "state" => "OPEN",
                    "url" => "",
                    "updatedAt" => "2023-02-01T13:29:20Z"
                ],
            ]
        ];
    }
}
