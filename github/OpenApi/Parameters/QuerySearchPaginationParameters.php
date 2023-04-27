<?php

namespace Webid\OctoolsGithub\OpenApi\Parameters;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class QuerySearchPaginationParameters extends CursorPaginatedParameters
{
    /**
     * @return Parameter[]
     */
    public function build(): array
    {
        return [
            ...parent::build(),
            Parameter::query()
                ->name('query')
                ->required(true)
                ->in(Parameter::IN_PATH)
                ->schema(Schema::string()),
        ];
    }
}
