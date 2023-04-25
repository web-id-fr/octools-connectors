<?php

namespace Webid\OctoolsGithub\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class ErrorGithubRepositoryNotFoundResponse extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        return Response::notFound('ErrorGithubRepositoryNotFound')
            ->description('Repository not found error')
            ->content(
                MediaType::json()->schema(Schema::object()->properties(
                    Schema::string('error')
                        ->example('Repository not found.'),
                ))
            );
    }
}
