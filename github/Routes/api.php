<?php

use Illuminate\Support\Facades\Route;
use Webid\OctoolsGithub\Http\Controllers\GithubController;

Route::group(config('octools.api_routes_group'), function () {
    Route::name('github.')->prefix('github')->group(function () {
        Route::get('/company-employees', [GithubController::class, 'getCompanyEmployees'])->name('company-employees');
        Route::get('/company-repositories', [GithubController::class, 'getCompanyRepositories'])->name('company-repositories');
        Route::get('/repository/{repositoryName}', [GithubController::class, 'getCompanyRepositoryByName'])->name('repository');
        Route::get('/issues/{repositoryName}', [GithubController::class, 'getCompanyRepositoryIssues'])->name('issues');
        Route::get('/pull-requests/{repositoryName}/{member}', [GithubController::class, 'getUserPullRequestsByRepository'])->name('user-pull-requests');
        Route::get('/pull-requests/{repositoryName}', [GithubController::class, 'getRepositoryPullRequests'])->name('repository-pull-requests');
        Route::get('/search-repositories/{query}', [GithubController::class, 'searchRepositories'])->name('search-repositories');
        Route::get('/search-issues/{query}', [GithubController::class, 'searchIssues'])->name('search-issues');
        Route::get('/search-pull-requests/{query}', [GithubController::class, 'searchPullRequests'])->name('search-pull-requests');
    });
});
