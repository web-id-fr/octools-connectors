<?php

namespace Webid\OctoolsGryzzly\OpenApi\Parameters;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;

class CompanyEmployeeByMemberIdParameters extends ParametersFactory
{
    /**
     * @return Parameter[]
     */
    public function build(): array
    {
        return [
            Parameter::query()
                ->in(Parameter::IN_PATH)
                ->name('member')
                ->required(true)
                ->schema(Schema::integer()->minimum(0))
                ->description('Octools Member ID'),
        ];
    }
}
