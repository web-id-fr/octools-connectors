<?php

declare(strict_types=1);

namespace Webid\OctoolsGryzzly\Api;

use Webid\Octools\Shared\CursorPaginator;
use Webid\OctoolsGryzzly\Api\Entities\Declaration;
use Webid\OctoolsGryzzly\Api\Entities\GryzzlyCredentials;
use Webid\OctoolsGryzzly\Api\Entities\Project;
use Webid\OctoolsGryzzly\Api\Entities\Task;
use Webid\OctoolsGryzzly\Api\Entities\User;

class FakeGryzzlyApiService implements GryzzlyApiServiceInterface
{
    public function getEmployees(GryzzlyCredentials $credentials, array $parameters): CursorPaginator
    {
        $nodes = array_map(
            fn (array $item) => User::fromArray($item),
            [
                ['name' => 'Cédric', 'id' => '98dsq-hgf9-f8dqsc2-webid1', 'email' => 'cedric@example.com'],
                ['name' => 'Jo', 'id' => '98dsq-hgf9-f8dqsc2-webid2', 'email' => 'jo@example.com'],
                ['name' => 'Clément', 'id' => '98dsq-hgf9-f8dqsc2-webid3', 'email' => 'clement@example.com'],
            ]
        );

        $pageInfo = [
            'hasNextPage' => false,
            'hasPreviousPage' => false,
            'startCursor' => 0,
            'endCursor' => 3,
        ];

        return new CursorPaginator(10, $nodes, 3);
    }

    public function getEmployeeByUuid(GryzzlyCredentials $credentials, string $memberUuid): User
    {
        return User::fromArray([
            'name' => 'Cédric',
            'id' => '98dsq-hgf9-f8dqsc2-gsdfgbfd15',
            'email' => 'cedric@example.com'
        ]);
    }

    public function getEmployeeByEmail(GryzzlyCredentials $credentials, string $email): User
    {
        return User::fromArray([
            'name' => 'Cédric',
            'id' => '98dsq-hgf9-f8dqsc2-gsdfgbfd15',
            'email' => 'cedric@example.com'
        ]);
    }

    public function getProjects(GryzzlyCredentials $credentials, array $parameters): CursorPaginator
    {
        $nodes = array_map(
            fn (array $item) => Project::fromArray($item),
            [
                [
                    'id' => '98dsq-hgf9-f8dqsc2-mpi1',
                    'name' => 'MPI',
                ],
                [
                    'id' => '98dsq-hgf9-f8dqsc2-toad2',
                    'name' => 'TOAD',
                ],
                [
                    'id' => '98dsq-hgf9-f8dqsc2-luigi3',
                    'name' => 'Luigi',
                ],
            ]
        );

        return new CursorPaginator(10, $nodes, 3);
    }

    public function getTasksByProjects(
        GryzzlyCredentials $credentials,
        string $uuid,
        array $parameters
    ): CursorPaginator {
        $nodes = array_map(
            fn (array $item) => Task::fromArray($item),
            [
                [
                    'id' => '98dsq-hgf9-f8dqsc2-task1',
                    'name' => 'Create API',
                    'project_id' => '98dsq-hgf9-f8dqsc2-toad2',
                ],
                [
                    'id' => '98dsq-hgf9-f8dqsc2-task2',
                    'name' => 'Create landing',
                    'project_id' => '98dsq-hgf9-f8dqsc2-toad2',
                ],
                [
                    'id' => '98dsq-hgf9-f8dqsc2-task3',
                    'name' => 'Document API',
                    'project_id' => '98dsq-hgf9-f8dqsc2-toad2',
                ],
            ]
        );

        return new CursorPaginator(10, $nodes, 3);
    }

    public function getDeclarationsByEmployee(
        GryzzlyCredentials $credentials,
        string $memberUuid,
        array $parameters
    ): CursorPaginator {
        $nodes = array_map(
            fn (array $item) => Declaration::fromArray($item),
            [
                [
                    'id' => '98dsq-hgf9-f8dqsc2-decla1',
                    'duration' => 5400,
                    'date' => '2023-01-10 10:20:30',
                    'description' => 'description1',
                    'task_id' => '98dsq-hgf9-f8dqsc2-task1',
                    'user_id' => $memberUuid,
                ],
                [
                    'id' => '98dsq-hgf9-f8dqsc2-decla2',
                    'duration' => 3600,
                    'date' => '2023-01-21 21:22:23',
                    'description' => 'description2',
                    'task_id' => '98dsq-hgf9-f8dqsc2-task1',
                    'user_id' => $memberUuid,
                ],
                [
                    'id' => '98dsq-hgf9-f8dqsc2-decla3',
                    'duration' => 7200,
                    'date' => '2023-02-01 01:02:03',
                    'description' => 'description3',
                    'task_id' => '98dsq-hgf9-f8dqsc2-task3',
                    'user_id' => $memberUuid,
                ],
            ]
        );

        return new CursorPaginator(10, $nodes, 3);
    }
}
