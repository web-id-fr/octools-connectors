<?php

namespace Webid\OctoolsGithub\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;
use Webid\OctoolsGithub\OpenApi\Schemas\IssueResponseSchema;

class ListIssuesResponse extends ResponseFactory
{
    public function build(): Response
    {
        return Response::ok()->description('Successful response')->content(
            MediaType::json()->schema(
                Schema::object()->properties(
                    Schema::integer('perPage')->minimum(1),
                    Schema::array('items')->items(IssueResponseSchema::ref()),
                    Schema::integer('total')->nullable()->minimum(0),
                    Schema::string('cursor')
                        ->nullable()
                        ->example('Y3Vyc29yOnYyOpK5MjAyMy0wMS0yN1QyMDoyMDoxOCswMTowMM4aoNtE'),
                )
            )
        );
    }
}
