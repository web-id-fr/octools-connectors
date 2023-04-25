<?php

namespace Webid\OctoolsGithub\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class ErrorGithubIsNotConfiguredResponse extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        return Response::unauthorized('ErrorGithubIsNotConfigured')
            ->description('Github is not Configured')
            ->content(
                MediaType::json()->schema(Schema::object()->properties(
                    Schema::string('error')
                        ->example('Github is not configured for this workspace.'),
                ))
            );
    }
}
