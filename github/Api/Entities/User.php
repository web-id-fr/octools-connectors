<?php

declare(strict_types=1);

namespace Webid\OctoolsGithub\Api\Entities;

class User
{
    public function __construct(
        public readonly string $login,
        public readonly ?string $name,
        public readonly ?string $email,
        public readonly ?string $avatarUrl,
    ) {
    }

    public static function fromGraphArray(array $item): self
    {
        return new self(
            $item['login'],
            $item['name'] ?? null,
            $item['email'] ?? null,
            $item['avatarUrl'] ?? null
        );
    }

    public static function graphAttributes(): string
    {
        return 'login, name, email, avatarUrl';
    }
}
