<?php

namespace Webid\OctoolsGithub\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Webid\OctoolsGithub\OpenApi\Schemas\IssueResponseSchema;

class ListIssuesResponse extends AbstractCursorPaginatedResponse
{
    public function build(): Response
    {
        return $this->buildAroundItems(IssueResponseSchema::ref());
    }
}
