<?php

namespace Webid\OctoolsSlack\OpenApi\Parameters;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class SearchMessagesParameters extends CursorPaginatedParameters
{
    /**
     * @return Parameter[]
     */
    public function build(): array
    {
        return [
            ...parent::build(),
            Parameter::query()
                ->in(Parameter::IN_PATH)
                ->name('query')
                ->required(true)
                ->schema(Schema::string()),
        ];
    }
}
