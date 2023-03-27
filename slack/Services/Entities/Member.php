<?php

namespace Webid\OctoolsSlack\Services\Entities;

use Webid\Octools\Models\Member as OctoMember;
use Webid\OctoolsSlack\Api\Entities\User;
use Webid\OctoolsSlack\OctoolsSlack;

class Member extends User
{
    public int $idOctoMember;

    /**
     * @param User|null $user
     * @param OctoMember|null $member
     * @return self|null
     */
    public static function fromOctoMember(?User $user, ?OctoMember $member): self|null
    {
        if (is_null($user) || is_null($member)) {
            return null;
        }

        $instance = new self(
            $member->getUsernameForService(OctoolsSlack::make()) ?? $user->id,
            $user->name ?? null,
            $member->fullname(),
            $member->email
        );

        /** @var int $idOctoMember */
        $idOctoMember = $member->getKey();

        $instance->idOctoMember = $idOctoMember;

        return $instance;
    }
}
