<?php

declare(strict_types=1);

namespace Webid\OctoolsGryzzly\Api;

use Webid\Octools\Shared\CursorPaginator;
use Webid\OctoolsGryzzly\Api\Entities\GryzzlyCredentials;
use Webid\OctoolsGryzzly\Api\Entities\User;

interface GryzzlyApiServiceInterface
{
    public function getEmployees(GryzzlyCredentials $credentials, array $parameters): CursorPaginator;

    public function getEmployeeByUuid(GryzzlyCredentials $credentials, string $memberUuid): User;

    public function getEmployeeByEmail(GryzzlyCredentials $credentials, string $email): User;

    public function getProjects(GryzzlyCredentials $credentials, array $parameters): CursorPaginator;

    public function getTasksByProjects(
        GryzzlyCredentials $credentials,
        string $uuid,
        array $parameters
    ): CursorPaginator;

    public function getDeclarationsByEmployee(
        GryzzlyCredentials $credentials,
        string $memberUuid,
        array $parameters
    ): CursorPaginator;
}
