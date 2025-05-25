<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ExcelController;

// Landing page
Route::get('/', fn() => view('landing'));

// Breeze auth
require __DIR__ . '/auth.php';

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // Roles CRUD
    Route::resource('roles', RoleController::class);

    // Users CRUD (admin only)
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class);
    });

    /* **************************************
       PROJECT ROUTES + EXPORT/IMPORT
    ************************************** */
    Route::prefix('projects')->name('projects.')->group(function () {
        // Static routes (trash, restore, force-delete, restore-all)
        Route::get('trash',                    [ProjectController::class, 'trash'])
             ->name('trash');
        Route::post('{id}/restore',            [ProjectController::class, 'restore'])
             ->name('restore');
        Route::delete('{id}/force-delete',     [ProjectController::class, 'forceDelete'])
             ->name('forceDelete');
        Route::post('restore-all',             [ProjectController::class, 'restoreAll'])
             ->name('restoreAll');

        // Export & Import shortcuts
        Route::post('export',                  [ProjectController::class, 'export'])
             ->name('export');
        Route::post('import',                  [ProjectController::class, 'import'])
             ->name('import');
        Route::get('export/download',          [ProjectController::class, 'downloadExport'])
             ->name('export.download');

        // CRUD utama (parameter setelah semua static routes)
        Route::get('/',                        [ProjectController::class, 'index'])
             ->name('index');
        Route::get('create',                   [ProjectController::class, 'create'])
             ->name('create');
        Route::post('/',                       [ProjectController::class, 'store'])
             ->name('store');
        Route::get('{project}',                [ProjectController::class, 'show'])
             ->name('show');
        Route::get('{project}/edit',           [ProjectController::class, 'edit'])
             ->name('edit');
        Route::put('{project}',                [ProjectController::class, 'update'])
             ->name('update');
        Route::delete('{project}',             [ProjectController::class, 'destroy'])
             ->name('destroy');
    });

    /* **************************************
       TASK ROUTES + EXPORT/IMPORT
    ************************************** */
    Route::prefix('tasks')->name('tasks.')->group(function () {
        // Static routes (trash, restore, force-delete, restore-all)
        Route::get('trash',                  [TaskController::class, 'trash'])
             ->name('trash');
        Route::post('{id}/restore',          [TaskController::class, 'restore'])
             ->name('restore');
        Route::delete('{id}/force-delete',   [TaskController::class, 'forceDelete'])
             ->name('forceDelete');
        Route::post('restore-all',           [TaskController::class, 'restoreAll'])
             ->name('restoreAll');

        // Export & Import
        Route::post('export',                [TaskController::class, 'export'])
             ->name('export');
        Route::post('import',                [TaskController::class, 'import'])
             ->name('import');
        Route::get('export/download',        [TaskController::class, 'downloadExport'])
             ->name('export.download');

        // CRUD utama (parameter setelah semua static routes)
        Route::get('/',                      [TaskController::class, 'index'])
             ->name('index');
        Route::get('create',                 [TaskController::class, 'create'])
             ->name('create');
        Route::post('/',                     [TaskController::class, 'store'])
             ->name('store');
        Route::get('{task}',                 [TaskController::class, 'show'])
             ->name('show');
        Route::get('{task}/edit',            [TaskController::class, 'edit'])
             ->name('edit');
        Route::put('{task}',                 [TaskController::class, 'update'])
             ->name('update');
        Route::delete('{task}',              [TaskController::class, 'destroy'])
             ->name('destroy');
    });

    /* **************************************
       DOCUMENT ROUTES + EXPORT/IMPORT
    ************************************** */
    Route::prefix('documents')->name('documents.')->group(function () {
        // Export & Import
        Route::post('export',                [DocumentController::class, 'export'])
             ->name('export');
        Route::post('import',                [DocumentController::class, 'import'])
             ->name('import');
        Route::get('export/download',        [DocumentController::class, 'downloadExport'])
             ->name('export.download');

        // CRUD utama
        Route::get('/',                      [DocumentController::class, 'index'])
             ->name('index');
        Route::get('create',                 [DocumentController::class, 'create'])
             ->name('create');
        Route::post('/',                     [DocumentController::class, 'store'])
             ->name('store');
        Route::get('{document}',             [DocumentController::class, 'show'])
             ->name('show');
        Route::get('{document}/edit',        [DocumentController::class, 'edit'])
             ->name('edit');
        Route::put('{document}',             [DocumentController::class, 'update'])
             ->name('update');
        Route::delete('{document}',          [DocumentController::class, 'destroy'])
             ->name('destroy');
    });
});

// Generic ExcelController routes (jika masih dipakai)
Route::middleware('auth')->group(function () {
    Route::get('{form}/export',        [ExcelController::class, 'exportForm'])
         ->name('excel.export');
    Route::get('{form}/import',        [ExcelController::class, 'showImportForm'])
         ->name('excel.import.form');
    Route::post('{form}/import',       [ExcelController::class, 'importForm'])
         ->name('excel.import');
});

// Debug
Route::get('/cek', fn() => dd(auth()->check(), auth()->user()));
