<?php

namespace Webid\OctoolsSlack\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Webid\OctoolsSlack\OpenApi\Schemas\UserResponseSchema;

class ListEmployeesResponse extends AbstractCursorPaginatedResponse
{
    public function build(): Response
    {
        return $this->buildAroundItems(UserResponseSchema::ref());
    }
}
