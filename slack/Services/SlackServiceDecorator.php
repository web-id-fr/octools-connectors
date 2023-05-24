<?php

namespace Webid\OctoolsSlack\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Webid\Octools\Models\Member as OctoolsMember;
use Webid\Octools\Shared\CursorPaginator;
use Webid\OctoolsSlack\Api\Entities\SlackCredentials;
use Webid\OctoolsSlack\Api\Entities\User;
use Webid\OctoolsSlack\Api\SlackApiServiceInterface;
use Webid\OctoolsSlack\OctoolsSlack;
use Webid\OctoolsSlack\Services\Entities\Member;
use Webid\OctoolsSlack\Services\Entities\Message;

class SlackServiceDecorator implements SlackApiServiceInterface
{
    public function __construct(
        private readonly SlackApiServiceInterface $apiService,
    ) {
    }

    public function getEmployees(SlackCredentials $credentials, array $parameters): CursorPaginator
    {
        $result = $this->apiService->getEmployees($credentials, $parameters);

        $slackMemberIds = Arr::pluck($result->items, 'id');

        /** @var Collection<int, OctoolsMember> $members */
        $members = OctoolsMember::query()
            ->with('services')
            ->havingServiceMemberKeyMatching(OctoolsSlack::make(), array_filter($slackMemberIds))
            ->get()
            ->keyBy(fn (OctoolsMember $member) => $member->getUsernameForService(OctoolsSlack::make()));

        foreach ($result->items as $key => $user) {
            if (array_key_exists($user->id, $members->toArray())) {
                /** @var OctoolsMember $member */
                $member = $members[$user->id];
                $result->items[$key] = Member::fromOctoMember($user, $member);
            }
        }

        return $result;
    }

    public function getSlackMemberByEmail(SlackCredentials $credentials, string $email): User
    {
        return $this->apiService->getSlackMemberByEmail($credentials, $email);
    }

    public function sendMessageToChannel(SlackCredentials $credentials, string $message, string $channel, string $blocks = null, string $attachments = null): void
    {
        $this->apiService->sendMessageToChannel($credentials, $message, $channel, $blocks, $attachments);
    }

    public function searchMessages(SlackCredentials $credentials, string $search, array $parameters): CursorPaginator
    {
        $result = $this->apiService->searchMessages($credentials, $search, $parameters);

        $slackMemberIds = Arr::pluck($result->items, 'user_id');

        /** @var Collection<int, OctoolsMember> $members */
        $members = OctoolsMember::query()
            ->with('services')
            ->havingServiceMemberKeyMatching(OctoolsSlack::make(), array_filter($slackMemberIds))
            ->get()
            ->keyBy(fn (OctoolsMember $member) => $member->getUsernameForService(OctoolsSlack::make()));

        foreach ($result->items as $key => $message) {
            if (array_key_exists($message->user_id, $members->toArray())) {
                /** @var OctoolsMember $member */
                $member = $members[$message->user_id];
                $result->items[$key] = Message::fromMessage($message, $member);
            }
        }

        return $result;
    }
}
