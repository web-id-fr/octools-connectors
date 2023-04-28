<?php

namespace Webid\OctoolsGryzzly\OpenApi\Parameters;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;

class TasksByProjectsUUIDParameters extends ParametersFactory
{
    /**
     * @return Parameter[]
     */
    public function build(): array
    {
        return [
            Parameter::query()
                ->in(Parameter::IN_PATH)
                ->name('project')
                ->required(true)
                ->schema(Schema::string()->format(Schema::FORMAT_UUID))
                ->description('Gryzzly Project UUID'),
        ];
    }
}
