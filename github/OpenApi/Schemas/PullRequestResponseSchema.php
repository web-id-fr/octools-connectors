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

class PullRequestResponseSchema extends SchemaFactory implements Reusable
{
    /**
     * @return AllOf|OneOf|AnyOf|Not|Schema
     */
    public function build(): SchemaContract
    {
        return Schema::object('GithubPullRequestResponse')
            ->properties(
                Schema::string('title')->example('Fix this complex issue'),
                Schema::integer('number')->example(123),
                Schema::string('url')
                    ->example('https://github.com/organization/your-repository/pull/123'),
                Schema::string('state')->enum('OPEN', 'MERGED', 'CLOSED')->example('OPEN'),
                MemberResponseSchema::ref('author')->nullable(),
                Schema::array('linkedIssues')->items(IssueResponseSchema::ref()),
                Schema::array('assignees')->items(MemberResponseSchema::ref()),
                Schema::array('reviewers')->items(MemberResponseSchema::ref()),
            );
    }
}
