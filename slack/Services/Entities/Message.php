<?php

namespace Webid\OctoolsSlack\Services\Entities;

class Message
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $channel_id,
        public readonly ?string $channel_name,
        public readonly ?string $user_id,
        public readonly ?string $username,
        public readonly int $idOctoMember,
        public readonly ?array $blocks,
    ) {
    }

    public static function fromMessage(
        \Webid\OctoolsSlack\Api\Entities\Message $message,
        \Webid\Octools\Models\Member $member,
    ): self {
        /** @var int $idOctoMember */
        $idOctoMember = $member->getKey();

        return new self(
            $message->id,
            $message->channel_id,
            $message->channel_name,
            $message->user_id,
            $message->username,
            $idOctoMember,
            $message->blocks ?? [],
        );
    }
}
