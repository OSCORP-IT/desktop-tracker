<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    // /**
    //  * Display a listing of the resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // function __construct() {
    //     $this->middleware('permission:role_view|role_create|role_edit|role_delete', ['only' => ['index', 'show']]);
    //     $this->middleware('permission:role_create', ['only' => ['create', 'store']]);
    //     $this->middleware('permission:role_edit', ['only' => ['edit', 'update']]);
    //     $this->middleware('permission:role_delete', ['only' => ['destroy']]);
    // }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $roles = Role::query()
            ->latest()
            ->get();

        return view('admin_panel.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $role_permissions = Permission::get();

        return view('admin_panel.roles.create', compact('role_permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'permission' => ['required']
        ]);

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        $role->syncPermissions($request->permission);

        return redirect()->to('admin-panel/roles')
            ->with('success', 'Created Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role) {
        $permission = Permission::get();

        $role_permissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $role->id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();
    
        return view('admin_panel.roles.edit', compact('role', 'permission', 'role_permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role) {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'permission' => ['required']
        ]);

        $role->name = $request->name;
        $role->description = $request->description;
        $role->save();
    
        $role->syncPermissions($request->permission);

        return redirect()->to('admin-panel/roles')
            ->with('success', 'Updated Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role) {
        if ($role->id == 1) {
            return abort(404);
        }

        $role->delete();

        return redirect()->to('admin-panel/dashboard/roles')
            ->with('success', 'Deleted Successfully.');
    }
}
