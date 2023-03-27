<?php

declare(strict_types=1);

namespace Webid\OctoolsGryzzly;

use Illuminate\Support\Facades\Event;
use Webid\OctoolsGryzzly\Api\FakeGryzzlyApiService;
use Webid\OctoolsGryzzly\Api\GryzzlyApiService;
use Webid\OctoolsGryzzly\Api\GryzzlyApiServiceInterface;
use Webid\OctoolsGryzzly\Listeners\GuessGryzzlyMemberUuid;
use Webid\Octools\OctoolsService;
use Webid\Octools\Shared\BaseOctoolsServiceProvider;

class OctoolsGryzzlyServiceProvider extends BaseOctoolsServiceProvider
{
    protected function service(): OctoolsService
    {
        return OctoolsGryzzly::make();
    }

    protected function serviceProviderPath(): string
    {
        return __DIR__;
    }

    public function register(): void
    {
        parent::register();

        $this->app->bind(GryzzlyApiServiceInterface::class, match (config('octools-gryzzly.api_driver')) {
            'fake' => FakeGryzzlyApiService::class,
            default => GryzzlyApiService::class,
        });
    }

    public function boot(): void
    {
        parent::boot();

        Event::listen("member_service_set:{$this->service->name}", [GuessGryzzlyMemberUuid::class, 'handle']);
    }
}
