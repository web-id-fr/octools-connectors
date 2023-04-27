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

class MemberResponseSchema extends SchemaFactory implements Reusable
{
    /**
     * @return AllOf|OneOf|AnyOf|Not|Schema
     */
    public function build(): SchemaContract
    {
        return Schema::object('GithubMemberResponse')
            ->properties(
                Schema::string('login')->example('johndoe'),
                Schema::string('name')->nullable()->example(null),
                Schema::string('email')->nullable()->example('johndoe@example.com'),
                Schema::string('url')
                    ->nullable()
                    ->example('https://avatars.githubusercontent.com/u/1111222233334444?v=4'),
                Schema::integer('idOctoMember')
                    ->minimum(1)
                    ->nullable()
                    ->example('123'),
            );
    }
}
