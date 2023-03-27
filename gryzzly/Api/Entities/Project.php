<?php

declare(strict_types=1);

namespace Webid\OctoolsGryzzly\Api\Entities;

class Project
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self($data['id'], $data['name']);
    }
}
