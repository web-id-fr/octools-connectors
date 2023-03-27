<?php

declare(strict_types=1);

namespace Webid\OctoolsGryzzly\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Webid\Octools\Models\Member as OctoMember;
use Webid\Octools\Shared\CursorPaginator;
use Webid\OctoolsGryzzly\Api\Entities\GryzzlyCredentials;
use Webid\OctoolsGryzzly\Api\Entities\User;
use Webid\OctoolsGryzzly\Api\GryzzlyApiServiceInterface;
use Webid\OctoolsGryzzly\OctoolsGryzzly;
use Webid\OctoolsGryzzly\Services\Entities\Declaration;
use Webid\OctoolsGryzzly\Services\Entities\Member;

class GryzzlyServiceDecorator implements GryzzlyApiServiceInterface
{
    public function __construct(
        private readonly GryzzlyApiServiceInterface $apiService,
    ) {
    }

    public function getEmployees(GryzzlyCredentials $credentials, array $parameters): CursorPaginator
    {
        $result = $this->apiService->getEmployees($credentials, $parameters);

        $uuids = Arr::pluck($result->items, 'uuid');

        /** @var Collection $members */
        $members = OctoMember::query()
            ->with('services')
            ->havingServiceMemberKeyMatching(OctoolsGryzzly::make(), array_filter($uuids))
            ->get()
            ->keyBy(fn(OctoMember $member) => $member->getUsernameForService(OctoolsGryzzly::make()));

        foreach ($result->items as $key => $user) {
            if (array_key_exists($user->uuid, $members->toArray())) {
                /** @var OctoMember $member */
                $member = $members[$user->uuid];
                $result->items[$key] = Member::fromOctoMember($user, $member);
            }
        }

        return $result;
    }

    public function getEmployeeByUuid(GryzzlyCredentials $credentials, string $memberUuid): User
    {
        return $this->apiService->getEmployeeByUuid($credentials, $memberUuid);
    }

    public function getEmployeeByEmail(GryzzlyCredentials $credentials, string $email): User
    {
        return $this->apiService->getEmployeeByEmail($credentials, $email);
    }

    public function getProjects(GryzzlyCredentials $credentials, array $parameters): CursorPaginator
    {
        return $this->apiService->getProjects($credentials, $parameters);
    }

    public function getTasksByProjects(
        GryzzlyCredentials $credentials,
        string $uuid,
        array $parameters
    ): CursorPaginator {
        return $this->apiService->getTasksByProjects($credentials, $uuid, $parameters);
    }

    public function getDeclarationsByEmployee(
        GryzzlyCredentials $credentials,
        string $memberUuid,
        array $parameters
    ): CursorPaginator {
        $result = $this->apiService->getDeclarationsByEmployee($credentials, $memberUuid, $parameters);

        /** @var Collection $members */
        $idOctoMember = OctoMember::query()
            ->with('services')
            ->havingServiceMemberKeyMatching(OctoolsGryzzly::make(), $memberUuid)
            ->first()
            ?->getKey();

        foreach ($result->items as $key => $declaration) {
            $result->items[$key] = Declaration::fromDeclaration($declaration, $idOctoMember);
        }

        return $result;
    }
}
