<?php

namespace Webid\OctoolsSlack\Api;

use Webid\Octools\Shared\CursorPaginator;
use Webid\OctoolsSlack\Api\Entities\SlackCredentials;
use Webid\OctoolsSlack\Api\Entities\User;

interface SlackApiServiceInterface
{
    public function getEmployees(SlackCredentials $credentials, array $parameters): CursorPaginator;

    public function getSlackMemberByEmail(SlackCredentials $credentials, string $email): User;

    public function sendMessageToChannel(SlackCredentials $credentials, string $message, string $channel): void;

    public function searchMessages(SlackCredentials $credentials, string $search, array $parameters): CursorPaginator;
}
