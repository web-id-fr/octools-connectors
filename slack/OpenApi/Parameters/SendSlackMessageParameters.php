<?php

namespace Webid\OctoolsSlack\OpenApi\Parameters;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;

class SendSlackMessageParameters extends ParametersFactory
{
    /**
     * @return Parameter[]
     */
    public function build(): array
    {
        return [
            Parameter::query()
                ->name('message')
                ->required(true)
                ->schema(Schema::string()),
            Parameter::query()
                ->name('channel')
                ->required(true)
                ->schema(Schema::string()),
        ];
    }
}
