<?php

declare(strict_types=1);

namespace Webid\OctoolsGryzzly\Api\Entities;

class GryzzlyCredentials
{
    public function __construct(
        public readonly string $token,
    ) {
    }
}
