<?php

declare(strict_types=1);

namespace Webid\OctoolsGryzzly\Api\Entities;

class Declaration
{
    public function __construct(
        public readonly string $id,
        public readonly string $duration,
        public readonly string $date,
        public readonly ?string $description,
        public readonly string $taskId,
        public readonly string $userId,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['duration'],
            $data['date'],
            $data['description'] ?? null,
            $data['task_id'],
            $data['user_id'],
        );
    }
}
