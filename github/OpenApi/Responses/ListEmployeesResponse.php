<?php

namespace Webid\OctoolsGithub\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Webid\OctoolsGithub\OpenApi\Schemas\EmployeeResponseSchema;

class ListEmployeesResponse extends AbstractCursorPaginatedResponse
{
    public function build(): Response
    {
        return $this->buildAroundItems(EmployeeResponseSchema::ref());
    }
}
