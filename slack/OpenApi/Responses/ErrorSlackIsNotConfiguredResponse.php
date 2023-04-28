<?php

namespace Webid\OctoolsSlack\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class ErrorSlackIsNotConfiguredResponse extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        return Response::unauthorized('ErrorSlackIsNotConfigured')
            ->description('Slack is not Configured')
            ->content(
                MediaType::json()->schema(Schema::object()->properties(
                    Schema::string('error')
                        ->example('Slack is not configured for this workspace.'),
                ))
            );
    }
}
