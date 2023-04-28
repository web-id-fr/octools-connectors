<?php

namespace Webid\OctoolsGryzzly\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Webid\OctoolsGryzzly\OpenApi\Schemas\UserResponseSchema;

class ListEmployeesResponse extends AbstractCursorPaginatedResponse
{
    public function build(): Response
    {
        return $this->buildAroundItems(UserResponseSchema::ref());
    }
}
