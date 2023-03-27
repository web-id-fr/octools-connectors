<?php

declare(strict_types=1);

namespace Webid\OctoolsGithub\Api\Entities;

class GithubCredentials
{
    public function __construct(
        public readonly string $organization,
        public readonly string $token,
    ) {
    }
}
