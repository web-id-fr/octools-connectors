<?php

declare(strict_types=1);

namespace Webid\OctoolsGryzzly\Listeners;

use Webid\OctoolsGryzzly\Api\Entities\GryzzlyCredentials;
use Webid\OctoolsGryzzly\Api\Exceptions\EmployeeNotFoundException;
use Webid\OctoolsGryzzly\Api\GryzzlyApiServiceInterface;
use Webid\OctoolsGryzzly\OctoolsGryzzly;
use Webid\Octools\Models\Member;

class GuessGryzzlyMemberUuid
{
    public function __construct(
        private readonly GryzzlyApiServiceInterface $client
    ) {
    }

    public function handle(Member $member, array $payload): void
    {
        if (!empty($payload['uuid'])) {
            return;
        }

        $credentials = $member->workspace->services()->where('service', OctoolsGryzzly::make()->name)->first()?->config;

        if (empty($credentials['token'])) {
            return;
        }

        try {
            $gryzzlyMember = $this->client->getEmployeeByEmail(new GryzzlyCredentials($credentials['token']), $member->email);

            $member->services()->updateOrCreate(
                ['member_id' => $member->getKey(), 'service' => OctoolsGryzzly::make()->name],
                ['config' => ['uuid' => $gryzzlyMember->uuid]],
            );
        } catch (EmployeeNotFoundException) {}
    }
}
