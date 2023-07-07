<?php

declare(strict_types=1);

namespace Webid\OctoolsGithub\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Webid\OctoolsGithub\Api\Entities\GithubCredentials;
use Webid\OctoolsGithub\Api\Entities\Repository;
use Webid\OctoolsGithub\Api\GitHubApiServiceInterface;
use Webid\OctoolsGithub\OctoolsGithub;
use Webid\OctoolsGithub\Services\Entities\Member;
use Webid\OctoolsGithub\Services\Entities\PullRequest;
use Webid\Octools\Models\Member as OctoMember;
use Webid\Octools\Shared\CursorPaginator;

class GithubServiceDecorator implements GitHubApiServiceInterface
{
    public function __construct(
        private readonly GitHubApiServiceInterface $apiService,
    ) {
    }

    public function getCompanyRepositories(GithubCredentials $credentials, array $paginationParams): CursorPaginator
    {
        return $this->apiService->getCompanyRepositories($credentials, $paginationParams);
    }

    public function getCompanyRepositoryByName(GithubCredentials $credentials, string $repositoryName): Repository
    {
        return $this->apiService->getCompanyRepositoryByName($credentials, $repositoryName);
    }

    public function getCompanyRepositoryIssues(GithubCredentials $credentials,
        string $repositoryName,
        array $paginationParams
    ): CursorPaginator {
        return $this->apiService->getCompanyRepositoryIssues($credentials, $repositoryName, $paginationParams);
    }

    public function getCompanyEmployees(GithubCredentials $credentials, array $paginationParams): CursorPaginator
    {
        $result = $this->apiService->getCompanyEmployees($credentials, $paginationParams);

        $login = Arr::pluck($result->items, 'login');

        $members = OctoMember::query()
            ->with('services')
            ->havingServiceMemberKeyMatching(OctoolsGithub::make(), array_filter($login))
            ->get()
            ->keyBy(fn (OctoMember $member) => $member->getUsernameForService(OctoolsGithub::make()));

        foreach ($result->items as $key => $user) {
            if (array_key_exists($user->login, $members->toArray())) {
                /** @var OctoMember $member */
                $member = $members[$user->login];
                $result->items[$key] = Member::fromOctoMember($user, $member);
            }
        }

        return $result;
    }

    public function getRepositoryPullRequests(
        GithubCredentials $credentials,
        string $repositoryName,
        array $paginationParams
    ): CursorPaginator {
        $result = $this->apiService->getRepositoryPullRequests($credentials, $repositoryName, $paginationParams);

        return $this->pullRequestDecorator($result);
    }

    public function getUserPullRequestsByRepository(
        GithubCredentials $credentials,
        string $username,
        string $repositoryName,
        array $paginationParams
    ): CursorPaginator {
        $result = $this->apiService->getUserPullRequestsByRepository(
            $credentials,
            $username,
            $repositoryName,
            $paginationParams
        );

        return $this->pullRequestDecorator($result);
    }

    public function searchRepositories(
        GithubCredentials $credentials,
        string $search,
        array $paginationParams
    ): CursorPaginator {
        return $this->apiService->searchRepositories($credentials, $search, $paginationParams);
    }

    public function searchIssues(
        GithubCredentials $credentials,
        string $search,
        array $paginationParams
    ): CursorPaginator {
        return $this->apiService->searchIssues($credentials, $search, $paginationParams);
    }

    public function searchPullRequests(
        GithubCredentials $credentials,
        string $search,
        array $paginationParams
    ): CursorPaginator {
        $result = $this->apiService->searchPullRequests($credentials, $search, $paginationParams);

        return $this->pullRequestDecorator($result);
    }

    /**
     * @param CursorPaginator $result
     * @return CursorPaginator
     */
    private function pullRequestDecorator(CursorPaginator $result): CursorPaginator
    {
        $login = Arr::pluck($result->items, 'author.login');

        foreach ($result->items as $pullRequest) {
            $login = array_merge($login, Arr::pluck($pullRequest->assignees, 'login'));
            $login = array_merge($login, Arr::pluck($pullRequest->reviewers, 'login'));
        }

        /** @var Collection $members */
        $members = OctoMember::query()
            ->with('services')
            ->havingServiceMemberKeyMatching(OctoolsGithub::make(), array_filter($login))
            ->get()
            ->keyBy(fn(OctoMember $member) => $member->getUsernameForService(OctoolsGithub::make()));

        foreach ($result->items as $key => $pullRequest) {
            $assignees = [];
            $reviewers = [];

            /** @var OctoMember $member */
            $member = $members->get($pullRequest?->author?->login);

            if (!is_null($member)) {
                $author = Member::fromOctoMember(
                    $pullRequest->author,
                    $member
                );
            } else {
                $author = Member::fromOctoMember(
                    $pullRequest->author,
                    $member
                );
            }

            foreach ($pullRequest->assignees as $assignee) {
                /** @var OctoMember $member */
                $member = $members->get($assignee->login);
                $assignees[] = Member::fromOctoMember($assignee, $member);
            }

            foreach ($pullRequest->reviewers as $reviewer) {
                /** @var OctoMember $member */
                $member = $members->get($reviewer->login);
                $reviewers[] = Member::fromOctoMember($reviewer, $member);
            }

            $result->items[$key] = new PullRequest(
                $pullRequest->title,
                $pullRequest->number,
                $pullRequest->url,
                $pullRequest->state,
                $pullRequest->updatedAt,
                $author,
                $pullRequest->linkedIssues,
                $assignees,
                $reviewers
            );
        }

        return $result;
    }

    public function restGenericEndpoint(GithubCredentials $credentials, string $endpoint, array $parameters): array
    {
        return $this->apiService->restGenericEndpoint($credentials, $endpoint, $parameters);
    }

    public function graphqlGenericEndpoint(GithubCredentials $credentials, array $parameters): array
    {
        return $this->apiService->graphqlGenericEndpoint($credentials, $parameters);
    }
}
