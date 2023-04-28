<?php

namespace Webid\OctoolsGryzzly\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class ErrorGryzzlyIsNotConfiguredResponse extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        return Response::unauthorized('ErrorGryzzlyIsNotConfigured')
            ->description('Gryzzly is not Configured')
            ->content(
                MediaType::json()->schema(Schema::object()->properties(
                    Schema::string('error')
                        ->example('Gryzzly is not configured for this workspace.'),
                ))
            );
    }
}
