<?php

declare(strict_types=1);

namespace Webid\OctoolsSlack;

use Webid\Octools\OctoolsService;

final class OctoolsSlack extends OctoolsService
{
    public static function make(): OctoolsSlack
    {
        return new self('slack', 'member_id', ['token']);
    }
}
