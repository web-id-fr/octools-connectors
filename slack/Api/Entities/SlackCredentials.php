<?php

namespace Webid\OctoolsSlack\Api\Entities;

class SlackCredentials
{
    public function __construct(
        public readonly string $token
    ) {
    }
}
