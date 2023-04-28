<?php

namespace Webid\OctoolsGryzzly\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Webid\OctoolsGryzzly\OpenApi\Schemas\DeclarationResponseSchema;

class ListDeclarationsResponse extends AbstractCursorPaginatedResponse
{
    public function build(): Response
    {
        return $this->buildAroundItems(DeclarationResponseSchema::ref());
    }
}
