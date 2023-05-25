<?php

declare(strict_types=1);

namespace Webid\OctoolsGryzzly\Api;

use Webid\Octools\Shared\CursorPaginator;
use Webid\OctoolsGryzzly\Api\Entities\Declaration;
use Webid\OctoolsGryzzly\Api\Entities\GryzzlyCredentials;
use Webid\OctoolsGryzzly\Api\Entities\Project;
use Webid\OctoolsGryzzly\Api\Entities\Task;
use Webid\OctoolsGryzzly\Api\Entities\User;
use Webid\OctoolsGryzzly\Api\Exceptions\EmployeeNotFoundException;
use Webid\OctoolsGryzzly\Api\Exceptions\GryzzlyInvalidJsonStructure;
use Exception;
use Illuminate\Config\Repository;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Webmozart\Assert\Assert;

class GryzzlyApiService implements GryzzlyApiServiceInterface
{
    private const PER_PAGE = 30;

    public function __construct(
        private Factory $http,
        private Repository $config,
    ) {
    }

    /**
     * @throws Exception
     * @throws GryzzlyInvalidJsonStructure
     */
    public function getEmployees(GryzzlyCredentials $credentials, array $parameters): CursorPaginator
    {
        $paginationParams = $this->buildPaginationParameters($parameters);

        /** @var Response $response */
        $response = $this->auth($credentials)->post('users.list', $paginationParams);

        /** @var array $json */
        $json = $response->json();

        if (empty($json['data'])) {
            throw new Exception('no employees found');
        }

        $nodes = array_map(
            fn (array $item) => User::fromArray($item),
            $json['data']
        );

        return $this->cursorPaginatorFromResponse($json, $nodes);
    }

    /**
     * @throws EmployeeNotFoundException
     */
    public function getEmployeeByUuid(GryzzlyCredentials $credentials, string $memberUuid): User
    {
        /** @var Response $response */
        $response = $this->auth($credentials)->post('users.get', ['user_id' => $memberUuid]);

        /** @var array $json */
        $json = $response->json();

        if (empty($json)) {
            throw new EmployeeNotFoundException();
        }

        return User::fromArray($json);
    }

    /**
     * @throws EmployeeNotFoundException
     * @throws Exception
     * @throws GryzzlyInvalidJsonStructure
     */
    public function getEmployeeByEmail(GryzzlyCredentials $credentials, string $email): User
    {
        $after = 0;
        $hasNextPage = true;

        while ($hasNextPage) {
            $response = $this->getEmployees($credentials, ['cursor' => $after]);

            /** @var array<User> $employees */
            $employees = $response->items;
            foreach ($employees as $employee) {
                if ($employee->email === $email) {
                    return $employee;
                }
            }

            $after = $response->cursor;
            $hasNextPage = !empty($response->cursor);
        }

        throw new EmployeeNotFoundException();
    }

    /**
     * @throws Exception
     * @throws GryzzlyInvalidJsonStructure
     */
    public function getProjects(GryzzlyCredentials $credentials, array $parameters): CursorPaginator
    {
        $paginationParams = $this->buildPaginationParameters($parameters);

        /** @var Response $response */
        $response = $this->auth($credentials)->post('projects.list', $paginationParams);

        /** @var array $json */
        $json = $response->json();

        if (empty($json['data'])) {
            throw new Exception('no projects');
        }

        $nodes = array_map(
            fn (array $item) => Project::fromArray($item),
            $json['data']
        );

        return $this->cursorPaginatorFromResponse($json, $nodes);
    }

    /**
     * @throws Exception
     * @throws GryzzlyInvalidJsonStructure
     */
    public function getTasksByProjects(
        GryzzlyCredentials $credentials,
        string $uuid,
        array $parameters
    ): CursorPaginator {
        $paginationParams = $this->buildPaginationParameters($parameters);

        /** @var Response $response */
        $response = $this->auth($credentials)
            ->post(
                'tasks.list',
                [
                    ...$paginationParams,
                    'project_ids' => [$uuid]
                ]
            );

        /** @var array $json */
        $json = $response->json();

        if (empty($json['data'])) {
            throw new Exception('no tasks');
        }

        $nodes = array_map(
            fn (array $item) => Task::fromArray($item),
            $json['data']
        );

        return $this->cursorPaginatorFromResponse($json, $nodes);
    }

    /**
     * @throws Exception
     * @throws GryzzlyInvalidJsonStructure
     */
    public function getDeclarationsByEmployee(
        GryzzlyCredentials $credentials,
        string $memberUuid,
        array $parameters
    ): CursorPaginator {
        $paginationParams = $this->buildPaginationParameters($parameters);

        try {
            /** @var Response $response */
            $response = $this->auth($credentials)
                ->post(
                    'declarations.list',
                    [
                        ...$paginationParams,
                        'user_ids' => [$memberUuid],
                    ]
                );
        } catch (Exception $e) {
            return new CursorPaginator(30, []);
        }


        /** @var array $json */
        $json = $response->json();

        if (empty($json['data'])) {
            throw new Exception('no declarations');
        }

        $nodes = array_map(
            fn (array $item) => Declaration::fromArray($item),
            $json['data']
        );

        return $this->cursorPaginatorFromResponse($json, $nodes);
    }

    private function auth(GryzzlyCredentials $credentials): PendingRequest
    {
        /** @var string $url */
        $url = $this->config->get('octools-gryzzly.url');

        return $this->http
            ->throw()
            ->acceptJson()
            ->asJson()
            ->withToken($credentials->token)
            ->baseUrl($url);
    }

    private function buildPaginationParameters(array $parameters): array
    {
        $perPage = self::PER_PAGE;
        $offset = 0;

        if (isset($parameters['before']) && is_int($parameters['before'])) {
            $offset = max($parameters['before'] - $perPage, 0);
        }

        if (isset($parameters['cursor']) && is_int((int)$parameters['cursor'])) {
            $offset = max((int)$parameters['cursor'], 0);
        }

        return [
            ...$parameters,
            'limit' => $perPage,
            'offset' => $offset,
        ];
    }

    /**
     * @throws GryzzlyInvalidJsonStructure
     */
    public function cursorPaginatorFromResponse(
        array $json,
        array $nodes,
    ): CursorPaginator {

        $this->assertJsonStructure($json);

        return new CursorPaginator(
            $json['limit'],
            $nodes,
            intval($json['count']),
            $json['offset'] + $json['limit'] < $json['count'] ? (string) ($json['offset'] + $json['limit']) : null,
        );
    }

    /**
     * @throws GryzzlyInvalidJsonStructure
     */
    public function assertJsonStructure(array $json) : void
    {
        try {
            Assert::keyExists($json, 'limit');
            Assert::keyExists($json, 'count');
            Assert::keyExists($json, 'offset');
        } catch (\InvalidArgumentException $e) {
            throw new GryzzlyInvalidJsonStructure();
        }
    }
}
