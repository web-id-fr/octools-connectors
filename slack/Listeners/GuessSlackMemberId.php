<?php

declare(strict_types=1);

namespace Webid\OctoolsSlack\Listeners;

use Webid\Octools\Models\Member;
use Webid\OctoolsSlack\Api\Entities\SlackCredentials;
use Webid\OctoolsSlack\Api\Exceptions\SlackMemberNotFoundException;
use Webid\OctoolsSlack\Api\SlackApiServiceInterface;
use Webid\OctoolsSlack\OctoolsSlack;

class GuessSlackMemberId
{
    public function __construct(
        private readonly SlackApiServiceInterface $client
    ) {
    }

    /**
     * @param Member $member
     * @param array $payload
     * @return void
     */
    public function handle(Member $member, ?string $identifier): void
    {
        if (!empty($identifier)) {
            return;
        }

        $credentials = $member->workspace->services()->where('service', OctoolsSlack::make()->name)->first()?->config;

        if (empty($credentials['token'])) {
            return;
        }

        try {
            $slackMember = $this->client->getSlackMemberByEmail(new SlackCredentials($credentials['token']), $member->email);

            $member->services()->updateOrCreate(
                ['member_id' => $member->getKey(), 'service' => OctoolsSlack::make()->name],
                ['identifier' => $slackMember->id],
            );
        } catch (SlackMemberNotFoundException) {}

    }
}
