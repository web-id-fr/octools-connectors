<?php

namespace Webid\OctoolsGithub\OpenApi\Parameters;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ListCompanyRepositoryIssuesPaginationParameters extends CursorPaginatedParameters
{
    /**
     * @return Parameter[]
     */
    public function build(): array
    {
        return [
            Parameter::query()
                ->name('repositoryName')
                ->required(true)
                ->in(Parameter::IN_PATH)
                ->schema(Schema::string()),
            ...parent::build(),
            Parameter::query()
                ->name('state')
                ->required(false)
                ->schema(Schema::string()->enum('open', 'closed')),
            Parameter::query()
                ->name('sort')
                ->required(false)
                ->schema(Schema::string()->enum('created_at', 'updated_at')),
            Parameter::query()
                ->name('direction')
                ->required(false)
                ->schema(Schema::string()->enum('asc', 'desc')),
        ];
    }
}
