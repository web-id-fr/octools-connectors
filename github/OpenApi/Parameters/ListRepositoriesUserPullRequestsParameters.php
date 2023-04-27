<?php

namespace Webid\OctoolsGithub\OpenApi\Parameters;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;

class ListRepositoriesUserPullRequestsParameters extends ListRepositoriesPullRequestsParameters
{
    /**
     * @return Parameter[]
     */
    public function build(): array
    {
        return [
            ...parent::build(),
            Parameter::query()
                ->name('member')
                ->required(true)
                ->in(Parameter::IN_PATH)
                ->schema(Schema::string()),
        ];
    }
}
