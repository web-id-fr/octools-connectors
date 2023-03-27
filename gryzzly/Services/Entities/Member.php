<?php

declare(strict_types=1);

namespace Webid\OctoolsGryzzly\Services\Entities;

use Webid\Octools\Models\Member as OctoMember;
use Webid\OctoolsGryzzly\Api\Entities\User;
use Webid\OctoolsGryzzly\OctoolsGryzzly;

class Member extends User
{
    public ?int $idOctoMember;

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

        $instance = new self($member->getUsernameForService(OctoolsGryzzly::make()) ?? $user->uuid, $member->fullname(), $member->email);

        /** @var int $idOctoMember */
        $idOctoMember = $member->getKey();

        $instance->idOctoMember = $idOctoMember;

        return $instance;
    }
}
