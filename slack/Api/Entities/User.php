<?php

namespace Webid\OctoolsSlack\Api\Entities;

use JoliCode\Slack\Api\Model\ObjsUser;

class User
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $name,
        public readonly ?string $realName,
        public readonly ?string $email,
    ) {
    }

    public static function fromObjsUser(ObjsUser $user): self
    {
        $id = $user->getId();
        $profile = $user->getProfile();

        if (empty($id) || empty($profile)) {
            throw new \InvalidArgumentException('Incorrect User infos returned');
        }

        return new self($id, $profile->getName(), $profile->getRealName(), $profile->getEmail());
    }
}
