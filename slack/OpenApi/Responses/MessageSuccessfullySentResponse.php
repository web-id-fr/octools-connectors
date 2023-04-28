<?php

namespace Webid\OctoolsSlack\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class MessageSuccessfullySentResponse extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        return Response::unauthorized('MessageSuccessfullySent')
            ->description('Message send with success')
            ->content(
                MediaType::json()->schema(Schema::object()->properties(
                    Schema::string('message')
                        ->example('Message send with success'),
                ))
            );
    }
}
