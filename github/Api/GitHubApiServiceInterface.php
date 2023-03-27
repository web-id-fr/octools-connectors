<?php

declare(strict_types=1);

namespace Webid\OctoolsGithub\Api;

use Webid\OctoolsGithub\Api\Entities\GithubCredentials;
use Webid\Octools\Shared\CursorPaginator;
use Webid\OctoolsGithub\Api\Entities\Repository;

interface GitHubApiServiceInterface
{
    public function getCompanyRepositories(
        GithubCredentials $credentials,
        array $paginationParams
    ): CursorPaginator;

    public function getCompanyRepositoryByName(
        GithubCredentials $credentials,
        string $repositoryName
    ): Repository;

    public function getCompanyRepositoryIssues(
        GithubCredentials $credentials,
        string $repositoryName,
        array $paginationParams
    ): CursorPaginator;

    public function getCompanyEmployees(
        GithubCredentials $credentials,
        array $paginationParams
    ): CursorPaginator;

    public function getRepositoryPullRequests(
        GithubCredentials $credentials,
        string $repositoryName,
        array $paginationParams
    ): CursorPaginator;

    public function getUserPullRequestsByRepository(
        GithubCredentials $credentials,
        string $username,
        string $repositoryName,
        array $paginationParams
    ): CursorPaginator;

    public function searchRepositories(
        GithubCredentials $credentials,
        string $search,
        array $paginationParams
    ): CursorPaginator;

    public function searchIssues(
        GithubCredentials $credentials,
        string $search,
        array $paginationParams
    ): CursorPaginator;

    public function searchPullRequests(
        GithubCredentials $credentials,
        string $search,
        array $paginationParams
    ): CursorPaginator;
}
