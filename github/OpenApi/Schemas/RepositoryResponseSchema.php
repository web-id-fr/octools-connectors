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

class RepositoryResponseSchema extends SchemaFactory implements Reusable
{
    /**
     * @return AllOf|OneOf|AnyOf|Not|Schema
     */
    public function build(): SchemaContract
    {
        return Schema::object('GithubRepositoryResponse')
            ->properties(
                Schema::integer('databaseId')->example(123456789),
                Schema::string('name')->example('your-repository'),
                Schema::string('description')->nullable()->example(null),
                Schema::string('url')->example('https://github.com/organization/your-repository'),
                Schema::boolean('isFork')->example(false),
                Schema::string('sshUrl')
                    ->example('git@github.com:organization/your-repository.git'),
                Schema::string('updatedAt')->format(Schema::FORMAT_DATE_TIME),
            );
    }
}
