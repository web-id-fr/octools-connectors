<?php

declare(strict_types=1);

namespace Webid\OctoolsGithub\Services\Entities;

class PullRequest
{
    public function __construct(
        public readonly string $title,
        public readonly int $number,
        public readonly string $url,
        public readonly string $state,
        public readonly Member|null $author,
        public readonly array $linkedIssues,
        public readonly array $assignees,
        public readonly array $reviewers,
    ) {
    }

    public static function fromPullRequest(
        \Webid\OctoolsGithub\Api\Entities\PullRequest $pullRequest,
        Member $author,
        array $assignees,
        array $reviewers,
    ): self {
        return new self(
            $pullRequest->title,
            $pullRequest->number,
            $pullRequest->url,
            $pullRequest->state,
            $author,
            $pullRequest->linkedIssues,
            $assignees,
            $reviewers,
        );
    }
}
