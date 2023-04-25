<?php

namespace Webid\OctoolsGithub\OpenApi\Schemas;

use GoldSpecDigital\ObjectOrientedOAS\Contracts\SchemaContract;
use GoldSpecDigital\ObjectOrientedOAS\Objects\AllOf;
use GoldSpecDigital\ObjectOrientedOAS\Objects\AnyOf;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Not;
use GoldSpecDigital\ObjectOrientedOAS\Objects\OneOf;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;

class IssueResponseSchema extends SchemaFactory implements Reusable
{
    /**
     * @return AllOf|OneOf|AnyOf|Not|Schema
     */
    public function build(): SchemaContract
    {
        return Schema::object('IssueResponse')
            ->properties(
                Schema::string('title')->example('Need to fix something wrong'),
                Schema::integer('number')->example(123),
                Schema::string('state')->enum('OPEN', 'CLOSED')->example('OPEN'),
                Schema::string('url')
                    ->example('https://github.com/organization/your-repository/issues/123'),
                Schema::string('updatedAt')->format(Schema::FORMAT_DATE_TIME),
            );
    }
}
