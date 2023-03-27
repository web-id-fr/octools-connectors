<?php

declare(strict_types=1);

namespace Webid\OctoolsGryzzly\Api\Entities;

class User
{
    public function __construct(
        public readonly string $uuid,
        public readonly string $name,
        public readonly string $email,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['name'],
            $data['email'],
        );
    }
}
