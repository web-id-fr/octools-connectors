<?php

declare(strict_types=1);

namespace Webid\OctoolsGithub;

use Webid\OctoolsGithub\Api\FakeGitHubApiService;
use Webid\OctoolsGithub\Api\GitHubApiService;
use Webid\OctoolsGithub\Api\GitHubApiServiceInterface;
use Webid\Octools\OctoolsService;
use Webid\Octools\Shared\BaseOctoolsServiceProvider;

class OctoolsGithubServiceProvider extends BaseOctoolsServiceProvider
{
    protected function service(): OctoolsService
    {
        return OctoolsGithub::make();
    }

    protected function serviceProviderPath(): string
    {
        return __DIR__;
    }

    public function register(): void
    {
        parent::register();

        $this->app->bind(GitHubApiServiceInterface::class, match (config('octools-github.api_driver')) {
            'fake' => FakeGitHubApiService::class,
            default => GitHubApiService::class,
        });
    }

    public function boot(): void
    {
        parent::boot();

        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');
    }
}
