<?php

namespace Webid\OctoolsGryzzly\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Webid\OctoolsGryzzly\OpenApi\Schemas\TaskResponseSchema;

class ListTasksResponse extends AbstractCursorPaginatedResponse
{
    public function build(): Response
    {
        return $this->buildAroundItems(TaskResponseSchema::ref());
    }
}
