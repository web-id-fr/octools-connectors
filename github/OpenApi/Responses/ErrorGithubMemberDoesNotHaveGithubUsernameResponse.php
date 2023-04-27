<?php

namespace Webid\OctoolsGithub\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class ErrorGithubMemberDoesNotHaveGithubUsernameResponse extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        return Response::notFound('ErrorGithubMemberDoesNotHaveGithubUsernameResponse')
            ->description('Member does not have github username error')
            ->content(
                MediaType::json()->schema(Schema::object()->properties(
                    Schema::string('error')
                        ->example('Member does not have github username.'),
                ))
            );
    }
}
