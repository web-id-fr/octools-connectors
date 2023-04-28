<?php

namespace Webid\OctoolsGryzzly\OpenApi\Parameters;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class DeclarationsByMemberIdParameters extends CursorPaginatedParameters
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
                ->name('member')
                ->required(true)
                ->schema(Schema::integer()->minimum(0))
                ->description('Octools Member ID'),
        ];
    }
}
