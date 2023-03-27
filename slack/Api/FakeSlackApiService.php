<?php

namespace Webid\OctoolsSlack\Api;

use Webid\Octools\Shared\CursorPaginator;
use Webid\OctoolsSlack\Api\Entities\SlackCredentials;
use Webid\OctoolsSlack\Api\Entities\User;

class FakeSlackApiService implements SlackApiServiceInterface
{
    public function getEmployees(SlackCredentials $credentials, array $parameters): CursorPaginator
    {
        return new CursorPaginator(
            10,
            [
                '1' => 'Cédric',
                '2' => 'Jo',
                '3' => 'Clément',
            ],
        );
    }

    public function getSlackMemberByEmail(SlackCredentials $credentials, string $email): User
    {
        return new User('UFSQ9BC2V', 'Dupont', 'Eric', 'eric@dupont.com');
    }

    public function sendMessageToChannel(SlackCredentials $credentials, string $message, string $channel): void
    {
        //
    }

    public function searchMessages(SlackCredentials $credentials, string $search, array $parameters): CursorPaginator
    {
        return new CursorPaginator(
            10,
            [
                '1' => 'Salut',
                '2' => 'MEP',
                '3' => 'TOAD',
            ]
        );
    }
}
