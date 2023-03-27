<?php

namespace Webid\OctoolsSlack\Api\Entities;

class Message
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $channel_id,
        public readonly ?string $channel_name,
        public readonly ?string $user_id,
        public readonly ?string $username,
        public readonly ?array $blocks,
    ) {
    }

    public static function fromArray(array $item): self
    {
        return new self(
            $item['iid'],
            $item['channel']['id'],
            $item['channel']['name'],
            $item['user'],
            $item['username'],
            $item['blocks'] ?? [],
        );
    }
}
