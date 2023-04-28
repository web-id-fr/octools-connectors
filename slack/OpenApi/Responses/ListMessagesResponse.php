<?php

namespace Webid\OctoolsSlack\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Webid\OctoolsSlack\OpenApi\Schemas\MessageResponseSchema;

class ListMessagesResponse extends AbstractCursorPaginatedResponse
{
    public function build(): Response
    {
        return $this->buildAroundItems(MessageResponseSchema::ref());
    }
}
