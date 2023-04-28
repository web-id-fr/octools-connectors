<?php

namespace Webid\OctoolsGryzzly\OpenApi\Schemas;

use GoldSpecDigital\ObjectOrientedOAS\Contracts\SchemaContract;
use GoldSpecDigital\ObjectOrientedOAS\Objects\AllOf;
use GoldSpecDigital\ObjectOrientedOAS\Objects\AnyOf;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Not;
use GoldSpecDigital\ObjectOrientedOAS\Objects\OneOf;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;

class DeclarationResponseSchema extends SchemaFactory implements Reusable
{
    /**
     * @return AllOf|OneOf|AnyOf|Not|Schema
     */
    public function build(): SchemaContract
    {
        return Schema::object('GryzzlyDeclarationResponse')
            ->properties(
                Schema::string('id')->format(Schema::FORMAT_UUID),
                Schema::integer('duration')
                    ->description('Duration in seconds')
                    ->minimum(0)
                    ->example(3600),
                Schema::string('date')->format(Schema::FORMAT_DATE),
                Schema::string('description')->nullable()->example('Working on the fix #123'),
                Schema::string('taskId')->format(Schema::FORMAT_UUID),
                Schema::string('userId')->format(Schema::FORMAT_UUID),
                Schema::integer('idOctoMember')
                    ->minimum(1)
                    ->nullable()
                    ->example('123'),
            );
    }
}
