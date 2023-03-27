<?php

declare(strict_types=1);

namespace Webid\OctoolsSlack;

use Illuminate\Support\Facades\Event;
use Webid\Octools\OctoolsService;
use Webid\Octools\Shared\BaseOctoolsServiceProvider;
use Webid\OctoolsSlack\Api\FakeSlackApiService;
use Webid\OctoolsSlack\Api\SlackApiService;
use Webid\OctoolsSlack\Api\SlackApiServiceInterface;
use Webid\OctoolsSlack\Listeners\GuessSlackMemberId;

class OctoolsSlackServiceProvider extends BaseOctoolsServiceProvider
{
    protected function service(): OctoolsService
    {
        return OctoolsSlack::make();
    }

    protected function serviceProviderPath(): string
    {
        return __DIR__;
    }

    public function register(): void
    {
        parent::register();

        $this->app->bind(SlackApiServiceInterface::class, match (config('octools-slack.api_driver')) {
            'fake' => FakeSlackApiService::class,
            default => SlackApiService::class,
        });
    }

    public function boot(): void
    {
        parent::boot();

        Event::listen("member_service_set:{$this->service->name}", [GuessSlackMemberId::class, 'handle']);

        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
    }
}
