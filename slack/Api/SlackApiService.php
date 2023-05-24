<?php

namespace Webid\OctoolsSlack\Api;

use Illuminate\Support\Arr;
use JoliCode\Slack\Api\Model\ObjsUser;
use JoliCode\Slack\Client;
use JoliCode\Slack\ClientFactory;
use Webid\Octools\Shared\CursorPaginator;
use Webid\OctoolsSlack\Api\Entities\Message;
use Webid\OctoolsSlack\Api\Entities\SlackCredentials;
use Webid\OctoolsSlack\Api\Entities\User;
use Webid\OctoolsSlack\Api\Exceptions\CustomSlackMessageException;
use Webid\OctoolsSlack\Api\Exceptions\SlackMemberNotFoundException;

class SlackApiService implements SlackApiServiceInterface
{
    private const PER_PAGE = 30;

    public function getEmployees(SlackCredentials $credentials, array $parameters): CursorPaginator
    {
        $client = $this->getAuthenticateClient($credentials);

        $paginationParams = $this->buildPaginationParams($parameters);

        /** @var \JoliCode\Slack\Api\Model\UsersListGetResponse200 $userList */
        $userList = $client->usersList($paginationParams);

        /** @var array $members */
        $members = $userList->getMembers() ?? [];

        $meta = $userList->getResponseMetadata();

        $nodes = array_map(
            fn (ObjsUser $user) => User::fromObjsUser($user),
            $members
        );

        return new CursorPaginator(
            perPage: $paginationParams['limit'],
            items: $nodes,
            cursor: $meta?->getNextCursor()
        );
    }


    /**
     * @throws SlackMemberNotFoundException
     */
    public function getSlackMemberByEmail(SlackCredentials $credentials, string $email): User
    {
        $this->getAuthenticateClient($credentials);

        /** @var array<User> $employees */
        $employees = ($this->getEmployees($credentials, ['limit' => 100]))->items;
        foreach ($employees as $employee) {
            if ($employee->email === $email) {
                return $employee;
            }
        }

        throw new SlackMemberNotFoundException();
    }

    public function sendMessageToChannel(SlackCredentials $credentials, string $message, string $channel, string $blocks = null, string $attachments = null): void
    {
        $client = $this->getAuthenticateClient($credentials);
        $client->chatPostMessage(['channel' => $channel, 'text' => $message, 'attachments' => $attachments, 'blocks' => $blocks]);
    }

    /**
     * @throws CustomSlackMessageException
     */
    public function searchMessages(SlackCredentials $credentials, string $search, array $parameters): CursorPaginator
    {
        $client = $this->getAuthenticateClient($credentials);

        /** @var \JoliCode\Slack\Api\Model\SearchMessagesGetResponse200 $searchMessages */
        $searchMessages = $client->searchMessages(
            [
                'query' => $search,
                'page' => empty($parameters['cursor']) ? 1 : (int)$parameters['cursor']
            ]
        );

        /** @var array $messages */
        $messages = $searchMessages->getIterator()->getArrayCopy();

        if (empty($messages['messages']['matches'])) {
            throw new CustomSlackMessageException('No messages found.');
        }

        $nodes = array_map(
            fn (array $message) => Message::fromArray($message),
            $messages['messages']['matches']
        );

        return new CursorPaginator(
            perPage: self::PER_PAGE,
            items: $nodes,
            total: $messages['messages']['pagination']['total_count'],
            cursor: $messages['messages']['pagination']['page'] < $messages['messages']['pagination']['page_count'] ?
                (string)($messages['messages']['pagination']['page'] + 1) : null
        );
    }


    private function getAuthenticateClient(SlackCredentials $credentials): Client
    {
        return ClientFactory::create($credentials->token);
    }

    private function buildPaginationParams(array $parameters): array
    {
        $params = array_merge(
            ['limit' => self::PER_PAGE, 'cursor' => null],
            Arr::only($parameters, ['limit', 'cursor'])
        );

        return array_filter($params);
    }
}
