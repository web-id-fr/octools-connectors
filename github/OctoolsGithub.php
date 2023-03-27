<?php

declare(strict_types=1);

namespace Webid\OctoolsGithub;

use Webid\Octools\OctoolsService;

final class OctoolsGithub extends OctoolsService
{
    public static function make(): OctoolsGithub
    {
        return new self('github', 'username', ['organization', 'token']);
    }
}
