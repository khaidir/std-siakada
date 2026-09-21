<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Tampilkan halaman daftar roles & permissions.
     */
    public function index(): Response
    {
        $roles = Role::query()
            ->select(['id', 'name'])
            ->with('permissions:id,name')
            ->orderBy('name')
            ->get();

        $permissions = Permission::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        $items = $roles->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name')->values()->all(),
            ];
        })->values()->all();

        return Inertia::render('Admin/Roles', [
            'roles' => $items,
            'permissions' => $permissions->pluck('name')->values()->all(),
        ]);
    }

    /**
     * Update permission mapping untuk role.
     */
    public function updatePermissions(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'permissions' => ['present', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::findOrFail($validated['role_id']);
        $role->syncPermissions($validated['permissions']);

        return redirect()->back()->with('success', 'Permission role berhasil diperbarui.');
    }
}
