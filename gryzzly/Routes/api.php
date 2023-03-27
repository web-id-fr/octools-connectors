<?php


use Illuminate\Support\Facades\Route;
use Webid\OctoolsGryzzly\Http\Controllers\GryzzlyController;

Route::group(config('octools.api_routes_group'), function () {
    Route::name('gryzzly.')->prefix('gryzzly')->group(function () {
        Route::get('/company-employees', [GryzzlyController::class, 'getCompanyEmployees'])->name('company-employees');
        Route::get('/company-employee/{member}', [GryzzlyController::class, 'getCompanyEmployeeByUUID'])->name('company-employee');
        Route::get('/company-projects', [GryzzlyController::class, 'getCompanyProjects'])->name('company-projects');
        Route::get('/{project}/tasks', [GryzzlyController::class, 'getTasksByProjectsUUID'])->name('project-tasks');
        Route::get('/{member}/declarations', [GryzzlyController::class, 'getDeclarationsByEmployee'])->name('employee-declaration');
    });
});
