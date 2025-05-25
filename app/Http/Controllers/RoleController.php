<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        return view('roles.create');
    }



    public function show(Role $role)
    {
        return view('roles.show', compact('role'));
    }


    // === da ===
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
        ]);

        if (strtolower($request->name) === 'administrator') {
            return redirect()->back()->with('error', 'Role "administrator" tidak dapat dibuat ulang.');
        }

        Role::create($request->only('name'));

        return redirect()->route('roles.index')->with('success', 'Role berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
{
    $role = Role::findOrFail($id);

    if ($role->name === 'administrator') {
        return redirect()->route('roles.index')->with('error', 'Role "administrator" tidak dapat diubah.');
    }

    $request->validate([
        'name' => 'required|unique:roles,name,' . $id,
    ]);

    $role->update($request->only('name'));

    return redirect()->route('roles.index')->with('success', 'Role berhasil diperbarui.');
}


public function destroy(Role $role)
{
    if (strtolower($role->name) === 'administrator') {
        return redirect()->back()->with('error', 'Role "administrator" tidak dapat dihapus.');
    }

    $role->delete();

    return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus.');
}


    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }



}
