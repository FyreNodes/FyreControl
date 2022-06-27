<?php

namespace Pterodactyl\Http\Controllers\Admin;


use Illuminate\View\View;
use Illuminate\Http\Request;
use Pterodactyl\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Prologue\Alerts\AlertsMessageBag;
use Illuminate\Http\RedirectResponse;
use Pterodactyl\Http\Controllers\Controller;


class PermissionController extends Controller
{
    /**
     * @var \Prologue\Alerts\AlertsMessageBag
     */
    private $alert;

    /**
     * CreateServerController constructor.
     *
     * @param \Prologue\Alerts\AlertsMessageBag $alert
     */
    public function __construct(AlertsMessageBag $alert) {$this->alert = $alert;}

    public function index()
    {
        $roles = DB::table('permissions')->get();

        return view('admin.permissions.index', [
            'roles' => $roles,
        ]);
    }

    public function new()
    {
        return view('admin.permissions.new');
    }

    public function create(Request $request)
    {
        DB::table('permissions')->insert([
            'name' => $request->name,
            'color' => $request->color,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
            'p_settings' => $request->settings,
            'p_api' => $request->api,
            'p_permissions' => $request->permissions,
            'p_databases' => $request->databases,
            'p_locations' => $request->locations,
            'p_nodes' => $request->nodes,
            'p_servers' => $request->servers,
            'p_users' => $request->users,
            'p_mounts' => $request->mounts,
            'p_nests' => $request->nests,
            'p_knowledgebase' => $request->knowledgebase
        ]);

        $this->alert->success(
            trans('Role successfully created.')
        )->flash();

        return RedirectResponse::create('/admin/permissions/');
    }

    public function edit(Permission $permission)
    {
        $settings = $permission->p_settings;
        $api = $permission->p_api;
        $permissions = $permission->p_permissions;
        $databases = $permission->p_databases;
        $locations = $permission->p_locations;
        $nodes = $permission->p_nodes;
        $servers = $permission->p_servers;
        $users = $permission->p_users;
        $mounts = $permission->p_mounts;
        $nests = $permission->p_nests;
        $knowledgebase = $permission->p_knowledgebase;

        return view('admin.permissions.edit', [
            'role' => $permission,
            'settings' => $settings,
            'api' => $api,
            'permissions' => $permissions,
            'databases' => $databases,
            'locations' => $locations,
            'nodes' => $nodes,
            'servers' => $servers,
            'users' => $users,
            'mounts' => $mounts,
            'nests' => $nests,
            'knowledgebase' => $knowledgebase
        ]);
    }

    public function update(Request $request, $id)
    {
        DB::table('permissions')->where('id', '=', $id)->update([
            'name' => $request->name,
            'color' => $request->color,
            'updated_at' => \Carbon\Carbon::now(),
            'p_settings' => $request->settings,
            'p_api' => $request->api,
            'p_permissions' => $request->permissions,
            'p_databases' => $request->databases,
            'p_locations' => $request->locations,
            'p_nodes' => $request->nodes,
            'p_servers' => $request->servers,
            'p_users' => $request->users,
            'p_mounts' => $request->mounts,
            'p_nests' => $request->nests,
            'p_knowledgebase' => $request->knowledgebase
        ]);

        $this->alert->success(
            trans('Role successfully updated.')
        )->flash();

        return RedirectResponse::create('/admin/permissions/');
    }

    public function destroy($id)
    {
        $users = DB::table('users')->get();
        foreach($users as $user) {
            if($user->role == $id) {
                $this->alert->danger(
                    trans('This role is still in use by one or more users.')
                )->flash();
                return RedirectResponse::create('/admin/permissions/');
            }

        }

        DB::table('permissions')->where('id', '=', $id)->delete();

        $this->alert->success(
            trans('Role successfully deleted.')
        )->flash();

        return RedirectResponse::create('/admin/permissions/');
    }
}
