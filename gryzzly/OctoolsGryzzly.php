<?php

declare(strict_types=1);

namespace Webid\OctoolsGryzzly;

use Webid\Octools\OctoolsService;

final class OctoolsGryzzly extends OctoolsService
{
    public static function make(): OctoolsGryzzly
    {
        return new self('gryzzly', 'uuid', ['token']);
    }
}
