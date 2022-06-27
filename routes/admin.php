<?php

use Illuminate\Support\Facades\Route;
use Pterodactyl\Http\Controllers\Admin;
use Pterodactyl\Http\Middleware\Admin\Servers\ServerInstalled;

Route::get('/', [Admin\BaseController::class, 'index'])->name('admin.index');

/*
|--------------------------------------------------------------------------
| Location Controller Routes
|--------------------------------------------------------------------------
|
| Endpoint: /admin/locations
|
*/
Route::group(['prefix' => 'locations'], function () {
    Route::get('/', [Admin\LocationController::class, 'index'])->name('admin.locations');
    Route::get('/view/{location:id}', [Admin\LocationController::class, 'view'])->name('admin.locations.view');

    Route::post('/', [Admin\LocationController::class, 'create']);
    Route::patch('/view/{location:id}', [Admin\LocationController::class, 'update']);
});

/*
|--------------------------------------------------------------------------
| Database Controller Routes
|--------------------------------------------------------------------------
|
| Endpoint: /admin/databases
|
*/
Route::group(['prefix' => 'databases'], function () {
    Route::get('/', [Admin\DatabaseController::class, 'index'])->name('admin.databases');
    Route::get('/view/{host:id}', [Admin\DatabaseController::class, 'view'])->name('admin.databases.view');

    Route::post('/', [Admin\DatabaseController::class, 'create']);
    Route::patch('/view/{host:id}', [Admin\DatabaseController::class, 'update']);
    Route::delete('/view/{host:id}', [Admin\DatabaseController::class, 'delete']);
});

/*
|--------------------------------------------------------------------------
| Settings Controller Routes
|--------------------------------------------------------------------------
|
| Endpoint: /admin/settings
|
*/
Route::group(['prefix' => 'settings'], function () {
    Route::get('/', [Admin\Settings\IndexController::class, 'index'])->name('admin.settings');
    Route::get('/mail', [Admin\Settings\MailController::class, 'index'])->name('admin.settings.mail');
    Route::get('/advanced', [Admin\Settings\AdvancedController::class, 'index'])->name('admin.settings.advanced');
    Route::get('/api', [Admin\Settings\ApiController::class, 'index'])->name('admin.settings.api.index');
    Route::get('/api/new', [Admin\Settings\ApiController::class, 'create'])->name('admin.settings.api.new');

    Route::post('/mail/test', [Admin\Settings\MailController::class, 'test'])->name('admin.settings.mail.test');

    Route::patch('/', [Admin\Settings\IndexController::class, 'update']);
    Route::patch('/mail', [Admin\Settings\MailController::class, 'update']);
    Route::post('/api/new', [Admin\Settings\ApiController::class, 'store']);
    Route::patch('/advanced', [Admin\Settings\AdvancedController::class, 'update']);

    Route::delete('/api/revoke/{identifier}', [Admin\Settings\ApiController::class, 'delete'])->name('admin.settings.api.delete');
});

/*
|--------------------------------------------------------------------------
| User Controller Routes
|--------------------------------------------------------------------------
|
| Endpoint: /admin/users
|
*/
Route::group(['prefix' => 'users'], function () {
    Route::get('/', [Admin\UserController::class, 'index'])->name('admin.users');
    Route::get('/accounts.json', [Admin\UserController::class, 'json'])->name('admin.users.json');
    Route::get('/new', [Admin\UserController::class, 'create'])->name('admin.users.new');
    Route::get('/view/{user:id}', [Admin\UserController::class, 'view'])->name('admin.users.view');

    Route::post('/new', [Admin\UserController::class, 'store']);

    Route::patch('/view/{user:id}', [Admin\UserController::class, 'update']);
    Route::delete('/view/{user:id}', [Admin\UserController::class, 'delete']);
});

/*
|--------------------------------------------------------------------------
| Server Controller Routes
|--------------------------------------------------------------------------
|
| Endpoint: /admin/servers
|
*/
Route::group(['prefix' => 'servers'], function () {
    Route::get('/', [Admin\Servers\ServerController::class, 'index'])->name('admin.servers');
    Route::get('/new', [Admin\Servers\CreateServerController::class, 'index'])->name('admin.servers.new');
    Route::get('/view/{server:id}', [Admin\Servers\ServerViewController::class, 'index'])->name('admin.servers.view');

    Route::group(['middleware' => [ServerInstalled::class]], function () {
        Route::get('/view/{server:id}/details', [Admin\Servers\ServerViewController::class, 'details'])->name('admin.servers.view.details');
        Route::get('/view/{server:id}/build', [Admin\Servers\ServerViewController::class, 'build'])->name('admin.servers.view.build');
        Route::get('/view/{server:id}/startup', [Admin\Servers\ServerViewController::class, 'startup'])->name('admin.servers.view.startup');
        Route::get('/view/{server:id}/database', [Admin\Servers\ServerViewController::class, 'database'])->name('admin.servers.view.database');
        Route::get('/view/{server:id}/mounts', [Admin\Servers\ServerViewController::class, 'mounts'])->name('admin.servers.view.mounts');
    });

    Route::get('/view/{server:id}/manage', [Admin\Servers\ServerViewController::class, 'manage'])->name('admin.servers.view.manage');
    Route::get('/view/{server:id}/delete', [Admin\Servers\ServerViewController::class, 'delete'])->name('admin.servers.view.delete');

    Route::post('/new', [Admin\Servers\CreateServerController::class, 'store']);
    Route::post('/view/{server:id}/build', [Admin\ServersController::class, 'updateBuild']);
    Route::post('/view/{server:id}/startup', [Admin\ServersController::class, 'saveStartup']);
    Route::post('/view/{server:id}/database', [Admin\ServersController::class, 'newDatabase']);
    Route::post('/view/{server:id}/mounts', [Admin\ServersController::class, 'addMount'])->name('admin.servers.view.mounts.store');
    Route::post('/view/{server:id}/manage/toggle', [Admin\ServersController::class, 'toggleInstall'])->name('admin.servers.view.manage.toggle');
    Route::post('/view/{server:id}/manage/suspension', [Admin\ServersController::class, 'manageSuspension'])->name('admin.servers.view.manage.suspension');
    Route::post('/view/{server:id}/manage/reinstall', [Admin\ServersController::class, 'reinstallServer'])->name('admin.servers.view.manage.reinstall');
    Route::post('/view/{server:id}/manage/transfer', [Admin\Servers\ServerTransferController::class, 'transfer'])->name('admin.servers.view.manage.transfer');
    Route::post('/view/{server:id}/delete', [Admin\ServersController::class, 'delete']);

    Route::patch('/view/{server:id}/details', [Admin\ServersController::class, 'setDetails']);
    Route::patch('/view/{server:id}/database', [Admin\ServersController::class, 'resetDatabasePassword']);

    Route::delete('/view/{server:id}/database/{database:id}/delete', [Admin\ServersController::class, 'deleteDatabase'])->name('admin.servers.view.database.delete');
    Route::delete('/view/{server:id}/mounts/{mount:id}', [Admin\ServersController::class, 'deleteMount'])->name('admin.servers.view.mounts.delete');
});

/*
|--------------------------------------------------------------------------
| Node Controller Routes
|--------------------------------------------------------------------------
|
| Endpoint: /admin/nodes
|
*/
Route::group(['prefix' => 'nodes'], function () {
    Route::get('/', [Admin\Nodes\NodeController::class, 'index'])->name('admin.nodes');
    Route::get('/new', [Admin\NodesController::class, 'create'])->name('admin.nodes.new');
    Route::get('/view/{node:id}', [Admin\Nodes\NodeViewController::class, 'index'])->name('admin.nodes.view');
    Route::get('/view/{node:id}/settings', [Admin\Nodes\NodeViewController::class, 'settings'])->name('admin.nodes.view.settings');
    Route::get('/view/{node:id}/configuration', [Admin\Nodes\NodeViewController::class, 'configuration'])->name('admin.nodes.view.configuration');
    Route::get('/view/{node:id}/allocation', [Admin\Nodes\NodeViewController::class, 'allocations'])->name('admin.nodes.view.allocation');
    Route::get('/view/{node:id}/servers', [Admin\Nodes\NodeViewController::class, 'servers'])->name('admin.nodes.view.servers');
    Route::get('/view/{node:id}/system-information', Admin\Nodes\SystemInformationController::class);

    Route::post('/new', [Admin\NodesController::class, 'store']);
    Route::post('/view/{node:id}/allocation', [Admin\NodesController::class, 'createAllocation']);
    Route::post('/view/{node:id}/allocation/remove', [Admin\NodesController::class, 'allocationRemoveBlock'])->name('admin.nodes.view.allocation.removeBlock');
    Route::post('/view/{node:id}/allocation/alias', [Admin\NodesController::class, 'allocationSetAlias'])->name('admin.nodes.view.allocation.setAlias');
    Route::post('/view/{node:id}/settings/token', Admin\NodeAutoDeployController::class)->name('admin.nodes.view.configuration.token');

    Route::patch('/view/{node:id}/settings', [Admin\NodesController::class, 'updateSettings']);

    Route::delete('/view/{node:id}/delete', [Admin\NodesController::class, 'delete'])->name('admin.nodes.view.delete');
    Route::delete('/view/{node:id}/allocation/remove/{allocation:id}', [Admin\NodesController::class, 'allocationRemoveSingle'])->name('admin.nodes.view.allocation.removeSingle');
    Route::delete('/view/{node:id}/allocations', [Admin\NodesController::class, 'allocationRemoveMultiple'])->name('admin.nodes.view.allocation.removeMultiple');
});

/*
|--------------------------------------------------------------------------
| Mount Controller Routes
|--------------------------------------------------------------------------
|
| Endpoint: /admin/mounts
|
*/
Route::group(['prefix' => 'mounts'], function () {
    Route::get('/', [Admin\MountController::class, 'index'])->name('admin.mounts');
    Route::get('/view/{mount:id}', [Admin\MountController::class, 'view'])->name('admin.mounts.view');

    Route::post('/', [Admin\MountController::class, 'create']);
    Route::post('/{mount:id}/eggs', [Admin\MountController::class, 'addEggs'])->name('admin.mounts.eggs');
    Route::post('/{mount:id}/nodes', [Admin\MountController::class, 'addNodes'])->name('admin.mounts.nodes');

    Route::patch('/view/{mount:id}', [Admin\MountController::class, 'update']);

    Route::delete('/{mount:id}/eggs/{egg_id}', [Admin\MountController::class, 'deleteEgg']);
    Route::delete('/{mount:id}/nodes/{node_id}', [Admin\MountController::class, 'deleteNode']);
});

/*
|--------------------------------------------------------------------------
| Nest Controller Routes
|--------------------------------------------------------------------------
|
| Endpoint: /admin/nests
|
*/
Route::group(['prefix' => 'nests'], function () {
    Route::get('/', [Admin\Nests\NestController::class, 'index'])->name('admin.nests');
    Route::get('/new', [Admin\Nests\NestController::class, 'create'])->name('admin.nests.new');
    Route::get('/view/{nest:id}', [Admin\Nests\NestController::class, 'view'])->name('admin.nests.view');
    Route::get('/egg/new', [Admin\Nests\EggController::class, 'create'])->name('admin.nests.egg.new');
    Route::get('/egg/{egg:id}', [Admin\Nests\EggController::class, 'view'])->name('admin.nests.egg.view');
    Route::get('/egg/{egg:id}/export', [Admin\Nests\EggShareController::class, 'export'])->name('admin.nests.egg.export');
    Route::get('/egg/{egg:id}/variables', [Admin\Nests\EggVariableController::class, 'view'])->name('admin.nests.egg.variables');
    Route::get('/egg/{egg:id}/scripts', [Admin\Nests\EggScriptController::class, 'index'])->name('admin.nests.egg.scripts');

    Route::post('/new', [Admin\Nests\NestController::class, 'store']);
    Route::post('/import', [Admin\Nests\EggShareController::class, 'import'])->name('admin.nests.egg.import');
    Route::post('/egg/new', [Admin\Nests\EggController::class, 'store']);
    Route::post('/egg/{egg:id}/variables', [Admin\Nests\EggVariableController::class, 'store']);

    Route::put('/egg/{egg:id}', [Admin\Nests\EggShareController::class, 'update']);

    Route::patch('/view/{nest:id}', [Admin\Nests\NestController::class, 'update']);
    Route::patch('/egg/{egg:id}', [Admin\Nests\EggController::class, 'update']);
    Route::patch('/egg/{egg:id}/scripts', [Admin\Nests\EggScriptController::class, 'update']);
    Route::patch('/egg/{egg:id}/variables/{variable:id}', [Admin\Nests\EggVariableController::class, 'update'])->name('admin.nests.egg.variables.edit');

    Route::delete('/view/{nest:id}', [Admin\Nests\NestController::class, 'destroy']);
    Route::delete('/egg/{egg:id}', [Admin\Nests\EggController::class, 'destroy']);
    Route::delete('/egg/{egg:id}/variables/{variable:id}', [Admin\Nests\EggVariableController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Statistics Controller Routes
|--------------------------------------------------------------------------
|
| Endpoint: /admin/statistics
|
*/
Route::get('/statistics', [Admin\StatisticsController::class, 'index'])->name('admin.statistics');

/*
|--------------------------------------------------------------------------
| Announcements Controller Routes
|--------------------------------------------------------------------------
|
| Endpoint: /admin/announcements
|
*/
Route::group(['prefix' => 'announcements'], function () {
    Route::get('/', [Admin\AnnouncementsController::class, 'index'])->name('admin.announcements');
    Route::get('/new', [Admin\AnnouncementsController::class, 'create'])->name('admin.announcements.new');
    Route::get('/edit/{id}', [Admin\AnnouncementsController::class, 'edit'])->name('admin.announcements.edit');

    Route::post('/new', [Admin\AnnouncementsController::class, 'new'])->name('admin.announcements.create');
    Route::post('/edit/{id}', [Admin\AnnouncementsController::class, 'update'])->name('admin.announcements.update');

    Route::delete('/delete', [Admin\AnnouncementsController::class, 'delete'])->name('admin.announcements.delete');
});

/*
|--------------------------------------------------------------------------
| Knowledgebase Controller Routes
|--------------------------------------------------------------------------
|
| Endpoint: /knowledgebase
|
*/
Route::group(['prefix' => '/knowledgebase'], function () {
    Route::get('/', [Admin\Knowledgebase\IndexController::class, 'index'])->name('admin.knowledgebase');
    Route::patch('/update', [Admin\Knowledgebase\IndexController::class, 'update'])->name('admin.knowledgebase.update');

    Route::group(['prefix' => '/categories'], function () {
        Route::get('/', [Admin\Knowledgebase\CategoriesController::class, 'index'])->name('admin.knowledgebase.categories.index');
        Route::get('/new', [Admin\Knowledgebase\CategoriesController::class, 'new'])->name('admin.knowledgebase.categories.new');
        Route::get('/edit/{id}', [Admin\Knowledgebase\CategoriesController::class, 'edit'])->name('admin.knowledgebase.categories.edit');

        Route::post('/store', [Admin\Knowledgebase\CategoriesController::class, 'store'])->name('admin.knowledgebase.categories.store');
        Route::patch('/update/{id}', [Admin\Knowledgebase\CategoriesController::class, 'update'])->name('admin.knowledgebase.categories.update');
        Route::delete('/delete/{id}', [Admin\Knowledgebase\CategoriesController::class, 'delete'])->name('admin.knowledgebase.categories.delete');
    });

    Route::group(['prefix' => '/questions'], function () {
        Route::get('/', [Admin\Knowledgebase\QuestionsController::class, 'index'])->name('admin.knowledgebase.questions.index');
        Route::get('/new', [Admin\Knowledgebase\QuestionsController::class, 'new'])->name('admin.knowledgebase.questions.new');
        Route::get('/edit/{id}', [Admin\Knowledgebase\QuestionsController::class, 'edit'])->name('admin.knowledgebase.questions.edit');

        Route::post('/store', [Admin\Knowledgebase\QuestionsController::class, 'store'])->name('admin.knowledgebase.questions.store');
        Route::patch('/update/{id}', [Admin\Knowledgebase\QuestionsController::class, 'update'])->name('admin.knowledgebase.questions.update');
        Route::delete('/delete{id}', [Admin\Knowledgebase\QuestionsController::class, 'delete'])->name('admin.knowledgebase.questions.delete');
    });
});

/*
|--------------------------------------------------------------------------
| Staff System Controller Routes
|--------------------------------------------------------------------------
|
| Endpoint: /staff
|
*/
Route::post('/staff/update/{id}', [Admin\StaffController::class, 'update'])->name('admin.staff.update');

/*
|--------------------------------------------------------------------------
| Billing System Routes
|--------------------------------------------------------------------------
|
| Endpoint: /admin/permissions
|
*/
Route::group(['prefix' => 'billing'], function () {
    Route::get('/', [Admin\Billing\GeneralController::class, 'index'])->name('admin.billing');
    Route::patch('/', [Admin\Billing\GeneralController::class, 'update'])->name('admin.billing.update');

    Route::group(['prefix' => 'plans'], function () {
        Route::get('/', [Admin\Billing\PlanController::class, 'index'])->name('admin.billing.plans');
        Route::get('/new', [Admin\Billing\PlanController::class, 'new'])->name('admin.billing.plans.new');
        Route::get('/edit/{plan:id}', [Admin\Billing\PlanController::class, 'edit'])->name('admin.billing.plans.edit');

        Route::post('/new', [Admin\Billing\PlanController::class, 'store'])->name('admin.billing.plans.store');
        Route::patch('/update/{plan:id}', [Admin\Billing\PlanController::class, 'update'])->name('admin.billing.plans.update');
        Route::delete('/delete/{plan:id}', [Admin\Billing\PlanController::class, 'delete'])->name('admin.billing.plans.delete');
    });

    Route::group(['prefix' => 'types'], function () {
        Route::get('/', [Admin\Billing\TypeController::class, 'index'])->name('admin.billing.types');
        Route::get('/new', [Admin\Billing\TypeController::class, 'new'])->name('admin.billing.types.new');
        Route::get('/edit/{type:id}', [Admin\Billing\TypeController::class, 'edit'])->name('admin.billing.types.edit');

        Route::post('/new', [Admin\Billing\TypeController::class, 'store'])->name('admin.billing.types.store');
        Route::patch('/update/{type:id}', [Admin\Billing\TypeController::class, 'update'])->name('admin.billing.types.update');
        Route::delete('/delete/{type:id}', [Admin\Billing\TypeController::class, 'delete'])->name('admin.billing.types.delete');
    });
});
