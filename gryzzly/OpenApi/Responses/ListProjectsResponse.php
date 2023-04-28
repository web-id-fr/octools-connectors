<?php

namespace Webid\OctoolsGryzzly\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Webid\OctoolsGryzzly\OpenApi\Schemas\UserResponseSchema;

class ListProjectsResponse extends AbstractCursorPaginatedResponse
{
    public function build(): Response
    {
        return $this->buildAroundItems(UserResponseSchema::ref());
    }
}
