<?php

use Illuminate\Support\Facades\Route;
use Pterodactyl\Http\Controllers\Api\Client;
use Pterodactyl\Http\Middleware\Api\Client\StaffMiddleware;
use Pterodactyl\Http\Middleware\ServerActivitySubject;
use Pterodactyl\Http\Middleware\AccountActivitySubject;
use Pterodactyl\Http\Middleware\RequireTwoFactorAuthentication;
use Pterodactyl\Http\Middleware\Api\Client\Server\ResourceBelongsToServer;
use Pterodactyl\Http\Middleware\Api\Client\Server\AuthenticateServerAccess;

/*
|--------------------------------------------------------------------------
| Client Control API
|--------------------------------------------------------------------------
|
| Endpoint: /api/client
|
*/
Route::get('/', [Client\ClientController::class, 'index'])->name('api:client.index');
Route::get('/role', [Client\GetUserRoleController::class, 'index']);
Route::get('/permissions', [Client\ClientController::class, 'permissions']);

Route::prefix('/account')->middleware(AccountActivitySubject::class)->group(function () {
    Route::prefix('/')->withoutMiddleware(RequireTwoFactorAuthentication::class)->group(function () {
        Route::get('/', [Client\AccountController::class, 'index'])->name('api:client.account');
        Route::get('/two-factor', [Client\TwoFactorController::class, 'index']);
        Route::post('/two-factor', [Client\TwoFactorController::class, 'store']);
        Route::delete('/two-factor', [Client\TwoFactorController::class, 'delete']);
    });

    Route::put('/email', [Client\AccountController::class, 'updateEmail'])->name('api:client.account.update-email');
    Route::put('/password', [Client\AccountController::class, 'updatePassword'])->name('api:client.account.update-password');

    Route::get('/activity', Client\ActivityLogController::class)->name('api:client.account.activity');

    Route::get('/api-keys', [Client\ApiKeyController::class, 'index']);
    Route::post('/api-keys', [Client\ApiKeyController::class, 'store']);
    Route::delete('/api-keys/{identifier}', [Client\ApiKeyController::class, 'delete']);

    Route::prefix('/ssh-keys')->group(function () {
        Route::get('/', [Client\SSHKeyController::class, 'index']);
        Route::post('/', [Client\SSHKeyController::class, 'store']);
        Route::delete('/{identifier}', [Client\SSHKeyController::class, 'delete']);
    });

    Route::group(['prefix' => 'staff', 'middleware' => [StaffMiddleware::class]], function () {
        Route::get('/', [Client\StaffController::class, 'index']);
        Route::post('/request', [Client\StaffController::class, 'request']);
        Route::delete('/delete/{id}', [Client\StaffController::class, 'delete']);
    });
});

/*
|--------------------------------------------------------------------------
| Client Control API
|--------------------------------------------------------------------------
|
| Endpoint: /api/client/servers/{server}
|
*/
Route::group([ 'prefix' => '/servers/{server}', 'middleware' => [ ServerActivitySubject::class, AuthenticateServerAccess::class, ResourceBelongsToServer::class ]], function () {
    Route::get('/', [Client\Servers\ServerController::class, 'index'])->name('api:client:server.view');
    Route::get('/websocket', Client\Servers\WebsocketController::class)->name('api:client:server.ws');
    Route::get('/resources', Client\Servers\ResourceUtilizationController::class)->name('api:client:server.resources');

    Route::post('/command', [Client\Servers\CommandController::class, 'index']);
    Route::post('/power', [Client\Servers\PowerController::class, 'index']);

    Route::group(['prefix' => '/databases'], function () {
        Route::get('/', [Client\Servers\DatabaseController::class, 'index']);
        Route::post('/', [Client\Servers\DatabaseController::class, 'store']);
        Route::post('/{database}/rotate-password', [Client\Servers\DatabaseController::class, 'rotatePassword']);
        Route::delete('/{database}', [Client\Servers\DatabaseController::class, 'delete']);
    });

    Route::group(['prefix' => '/files'], function () {
        Route::get('/list', [Client\Servers\FileController::class, 'directory']);
        Route::get('/contents', [Client\Servers\FileController::class, 'contents']);
        Route::get('/download', [Client\Servers\FileController::class, 'download']);
        Route::put('/rename', [Client\Servers\FileController::class, 'rename']);
        Route::post('/copy', [Client\Servers\FileController::class, 'copy']);
        Route::post('/write', [Client\Servers\FileController::class, 'write']);
        Route::post('/compress', [Client\Servers\FileController::class, 'compress']);
        Route::post('/decompress', [Client\Servers\FileController::class, 'decompress']);
        Route::post('/delete', [Client\Servers\FileController::class, 'delete']);
        Route::post('/create-folder', [Client\Servers\FileController::class, 'create']);
        Route::post('/chmod', [Client\Servers\FileController::class, 'chmod']);
        Route::post('/pull', [Client\Servers\FileController::class, 'pull'])->middleware(['throttle:10,5']);
        Route::get('/upload', Client\Servers\FileUploadController::class);
    });

    Route::group(['prefix' => '/schedules'], function () {
        Route::get('/', [Client\Servers\ScheduleController::class, 'index']);
        Route::post('/', [Client\Servers\ScheduleController::class, 'store']);
        Route::get('/{schedule}', [Client\Servers\ScheduleController::class, 'view']);
        Route::post('/{schedule}', [Client\Servers\ScheduleController::class, 'update']);
        Route::post('/{schedule}/execute', [Client\Servers\ScheduleController::class, 'execute']);
        Route::delete('/{schedule}', [Client\Servers\ScheduleController::class, 'delete']);

        Route::post('/{schedule}/tasks', [Client\Servers\ScheduleTaskController::class, 'store']);
        Route::post('/{schedule}/tasks/{task}', [Client\Servers\ScheduleTaskController::class, 'update']);
        Route::delete('/{schedule}/tasks/{task}', [Client\Servers\ScheduleTaskController::class, 'delete']);
    });

    Route::group(['prefix' => '/network'], function () {
        Route::get('/allocations', [Client\Servers\NetworkAllocationController::class, 'index']);
        Route::post('/allocations', [Client\Servers\NetworkAllocationController::class, 'store']);
        Route::post('/allocations/{allocation}', [Client\Servers\NetworkAllocationController::class, 'update']);
        Route::post('/allocations/{allocation}/primary', [Client\Servers\NetworkAllocationController::class, 'setPrimary']);
        Route::delete('/allocations/{allocation}', [Client\Servers\NetworkAllocationController::class, 'delete']);
    });

    Route::group(['prefix' => '/users'], function () {
        Route::get('/', [Client\Servers\SubuserController::class, 'index']);
        Route::post('/', [Client\Servers\SubuserController::class, 'store']);
        Route::get('/{user}', [Client\Servers\SubuserController::class, 'view']);
        Route::post('/{user}', [Client\Servers\SubuserController::class, 'update']);
        Route::delete('/{user}', [Client\Servers\SubuserController::class, 'delete']);
    });

    Route::group(['prefix' => '/backups'], function () {
        Route::get('/', [Client\Servers\BackupController::class, 'index']);
        Route::post('/', [Client\Servers\BackupController::class, 'store']);
        Route::get('/{backup}', [Client\Servers\BackupController::class, 'view']);
        Route::get('/{backup}/download', [Client\Servers\BackupController::class, 'download']);
        Route::post('/{backup}/lock', [Client\Servers\BackupController::class, 'toggleLock']);
        Route::post('/{backup}/restore', [Client\Servers\BackupController::class, 'restore']);
        Route::delete('/{backup}', [Client\Servers\BackupController::class, 'delete']);
    });

    Route::group(['prefix' => '/startup'], function () {
        Route::get('/', [Client\Servers\StartupController::class, 'index']);
        Route::put('/variable', [Client\Servers\StartupController::class, 'update']);
    });

    Route::group(['prefix' => 'staff'], function () {
    	Route::get('/', [Client\Servers\StaffController::class, 'index']);
    	Route::group(['prefix' => '/{id}'], function () {
    		Route::post('/accept', [Client\Servers\StaffController::class, 'accept']);
    		Route::post('/deny', [Client\Servers\StaffController::class, 'deny']);
    	});
    });

    Route::group(['prefix' => 'remover'], function () {
    	Route::get('/', [Client\Servers\AutoRemoverController::class, 'index']);
    	Route::post('/add', [Client\Servers\AutoRemoverController::class, 'add']);
    	Route::delete('/remove/{id}', [Client\Servers\AutoRemoverController::class, 'remove']);
    });

    Route::group(['prefix' => '/logs'], function () {
        Route::get('/', [Client\Servers\LogsController::class, 'index']);
        Route::delete('/delete/{id}', [Client\Servers\LogsController::class, 'delete']);
    });

    Route::group(['prefix' => '/settings'], function () {
        Route::post('/rename', [Client\Servers\SettingsController::class, 'rename']);
        Route::post('/reinstall', [Client\Servers\SettingsController::class, 'reinstall']);
        Route::put('/docker-image', [Client\Servers\SettingsController::class, 'dockerImage']);
    });
});

Route::group(['prefix' => '/announcements'], function () {
    Route::get('/', [Client\AnnouncementsController::class, 'index']);
    Route::get('/{id}', [Client\AnnouncementsController::class, 'view']);
});

/*
|--------------------------------------------------------------------------
| Client Knowledgebase API
|--------------------------------------------------------------------------
|
| Endpoint: /api/client/knowledgebase
|
*/
Route::group(['prefix' => '/knowledgebase'], function () {
    Route::get('/categories', [Client\KnowledgebaseController::class, 'categories']);
    Route::get('/question/{id}', [Client\KnowledgebaseController::class, 'question']);
    Route::get('/questions/{id}', [Client\KnowledgebaseController::class, 'questions']);
});

/*
|--------------------------------------------------------------------------
| Client Billing API
|--------------------------------------------------------------------------
|
| Endpoint: /api/client/billing
|
*/
Route::group(['prefix' => '/billing'], function () {
    Route::get('/plans', [Client\Billing\StoreController::class, 'plans']);
    Route::get('/types', [Client\Billing\StoreController::class, 'types']);

    Route::group(['prefix' => '/subscriptions'], function () {
        Route::get('/callback/success', [Client\Billing\SubscriptionController::class, 'success']);
        Route::get('/callback/fail', [Client\Billing\SubscriptionController::class, 'fail']);
        Route::post('/new', [Client\Billing\SubscriptionController::class, 'subscribe']);
        Route::post('/terminate/{server}', [Client\Billing\SubscriptionController::class, 'terminate']);
    });
});
