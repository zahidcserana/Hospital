<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Role list
     *
     */
    public function index()
    {
        $data = Role::all();

        $param['data'] = $data;

        return Inertia::render('settings/common/role/index', ['param' => $param]);
    }

    /**
     * Create Role
     */
    public function create()
    {
        return Inertia::render('settings/common/role/create');
    }

    /**
     * Store Role
     *
     * @param  $request
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'unique:roles'],
        ])->validate();

        Role::create($request->all());

        return Redirect::route('roles.index')->with('message', 'Role Created Successfully.');
    }


    /**
     * Edit Role
     *
     * @param $id
     */
    public function edit(Role $role)
    {
        $data = Role::find($role->id);

        $param['data'] =  $data;

        return Inertia::render('settings/common/role/edit', ['param' => $param]);
    }

    /**
     * Update Role
     *
     * @param $request
     * @param  $id
     */
    public function update(Request $request, Role $role)
    {
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'unique:roles'],
        ])->validate();

        if ($request->has('id')) {
            Role::find($request->input('id'))->update($request->all());
            return redirect()->back()
                ->with('message', 'Role Updated Successfully.');
        }
    }

    /**
     * Destroy Role
     *
     * @param  $id
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            Role::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
