<?php

namespace Webid\OctoolsGithub\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Contracts\SchemaContract;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

abstract class AbstractCursorPaginatedResponse extends ResponseFactory
{
    /**
     * Wrap items array response inside object with pagination and cursor.
     *
     * @param SchemaContract $items
     * @return Response
     * @throws \GoldSpecDigital\ObjectOrientedOAS\Exceptions\InvalidArgumentException
     */
    protected function buildAroundItems(SchemaContract $items): Response
    {
        return Response::ok()->description('Successful response')->content(
            MediaType::json()->schema(
                Schema::object()->properties(
                    Schema::integer('perPage')->minimum(1),
                    Schema::array('items')->items($items),
                    Schema::integer('total')->nullable()->minimum(0),
                    Schema::string('cursor')
                        ->nullable()
                        ->example('Y3Vyc29yOnYyOpK5MjAyMy0wMS0yN1QyMDoyMDoxOCswMTowMM4aoNtE'),
                )
            )
        );
    }
}
