<?php

declare(strict_types=1);

namespace Webid\OctoolsGithub\Services\Entities;

use Webid\OctoolsGithub\Api\Entities\User;
use Webid\OctoolsGithub\OctoolsGithub;
use Webid\Octools\Models\Member as OctoolsMember;

class Member extends User
{
    public ?int $idOctoMember;

    /**
     * @param OctoolsMember|null $member
     * @param User|null $user
     * @return self|null
     */
    public static function fromOctoMember(?User $user, ?OctoolsMember $member): self|null
    {
        if (is_null($user) && is_null($member)) {
            return null;
        }

        if (!is_null($member)) {
            /** @var int|null $idOctoMember */
            $idOctoMember = $member->getKey() ?? null;
            $instance = new self(
                $member->getUsernameForService(OctoolsGithub::make()) ?? $user->login,
                $member->fullname(),
                $member->email,
                $user->avatarUrl
            );
            $instance->idOctoMember = $idOctoMember;
        } else {
            $instance = new self(
                $user->login,
                $user->name,
                $user->email,
                $user->avatarUrl
            );
            $instance->idOctoMember = null;
        }

        return $instance;
    }
}
