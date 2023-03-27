<?php

declare(strict_types=1);

namespace Webid\OctoolsGithub\Api\Entities;

/**
 * @property array<User> $assignees
 * @property array<User> $reviewers
 * @property array<Issue> $linkedIssues
 */
class PullRequest
{
    public function __construct(
        public readonly string $title,
        public readonly int $number,
        public readonly string $url,
        public readonly string $state,
        public readonly ?User $author,
        public readonly array $linkedIssues,
        public readonly array $assignees,
        public readonly array $reviewers,
    ) {
    }

    public static function fromGraphArray(array $data): self
    {
        $data['reviewers'] = array_map(
            fn (array $reviewRequest) => $reviewRequest['requestedReviewer'],
            $data['reviewRequests']['nodes'] ?? []
        );

        try {
            $author = $data['author'] ? User::fromGraphArray($data['author']) : null;
        } catch (\Throwable $e) {
            $author = null;
        }

        return new self(
            $data['title'],
            $data['number'],
            $data['url'],
            $data['state'],
            $author,
            array_map(
                fn(array $item) => Issue::fromGraphArray($item),
                $data['closingIssuesReferences']['nodes'] ?? []
            ),
            array_map(
                fn(array $item) => User::fromGraphArray($item),
                $data['assignees']['nodes'] ?? []
            ),
            array_map(
                fn(array $item) => User::fromGraphArray($item),
                $data['reviewers']
            ),
        );
    }

    public static function graphAttributes(): string
    {
        return '
            title,
            number,
            url,
            state,
            author {... on User {' . User::graphAttributes() . '}},
            repository {' . Repository::graphAttributes() . '},
            closingIssuesReferences (first: 5) {
                nodes {
                  ' . Issue::graphAttributes() . '
                }
            }
            assignees (first: 5) {
                nodes {' . User::graphAttributes() . '}
            }
            reviewRequests(first: 10) {
              nodes {
                requestedReviewer {
                  ... on User {' . User::graphAttributes() . '}
                }
              }
            }
        ';
    }
}
