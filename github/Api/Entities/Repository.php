<?php

declare(strict_types=1);

namespace Webid\OctoolsGithub\Api\Entities;

class Repository
{
    public function __construct(
        public readonly int $databaseId,
        public readonly string $name,
        public readonly ?string $description,
        public readonly string $url,
        public readonly bool $isFork,
        public readonly string $sshUrl,
        public readonly string $updatedAt,
    ) {
    }

    public static function fromGraphArray(array $item): self
    {
        return new self(
            $item['databaseId'],
            $item['name'],
            $item['description'],
            $item['url'],
            $item['isFork'],
            $item['sshUrl'],
            $item['updatedAt'],
        );
    }

    public static function graphAttributes(): string
    {
        return 'databaseId, name, description, url, isFork, sshUrl, updatedAt';
    }
}
