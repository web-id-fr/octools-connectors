<?php

namespace Webid\OctoolsGithub\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Webid\OctoolsGithub\OpenApi\Schemas\RepositoryResponseSchema;

class ListRepositoriesResponse extends AbstractCursorPaginatedResponse
{
    public function build(): Response
    {
        return $this->buildAroundItems(RepositoryResponseSchema::ref());
    }
}
