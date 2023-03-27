<?php

declare(strict_types=1);

namespace Webid\OctoolsGithub\Api\Entities;

class Issue
{
    public function __construct(
        public readonly string $title,
        public readonly int $number,
        public readonly string $state,
        public readonly string $url,
        public readonly string $updatedAt,
    ) {
    }

    public static function fromGraphArray(array $data): self
    {
        return new self(
            $data['title'],
            $data['number'],
            $data['state'],
            $data['url'],
            $data['updatedAt']
        );
    }

    public static function graphAttributes(): string
    {
        return 'title, number, state, url, updatedAt';
    }
}
