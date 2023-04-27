<?php

namespace Webid\OctoolsGithub\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Webid\OctoolsGithub\OpenApi\Schemas\PullRequestResponseSchema;

class ListPullRequestsResponse extends AbstractCursorPaginatedResponse
{
    public function build(): Response
    {
        return $this->buildAroundItems(PullRequestResponseSchema::ref());
    }
}
