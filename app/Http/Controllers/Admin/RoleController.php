<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Role inti yang tidak boleh dihapus/di-rename karena dipakai
     * oleh logika aplikasi (scoping data & gerbang akses).
     */
    private const PROTECTED_ROLES = ['admin', 'ceo', 'technician', 'client'];

    public function index()
    {
        $roles = Role::withCount(['permissions', 'users'])->orderBy('name')->get();

        return view('admin.roles.index', [
            'roles' => $roles,
            'protectedRoles' => self::PROTECTED_ROLES,
        ]);
    }

    public function create()
    {
        return view('admin.roles.create', [
            'permissionGroups' => $this->permissionGroups(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'name')],
        ]);

        $role = Role::create(['name' => Str::of($validated['name'])->lower()->slug('_')->value()]);
        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role \"{$role->name}\" berhasil dibuat.");
    }

    public function edit(Role $role)
    {
        return view('admin.roles.edit', [
            'role' => $role,
            'permissionGroups' => $this->permissionGroups(),
            'rolePermissions' => $role->permissions->pluck('name')->all(),
            'isProtected' => in_array($role->name, self::PROTECTED_ROLES, true),
            'isAdmin' => $role->name === 'admin',
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $isProtected = in_array($role->name, self::PROTECTED_ROLES, true);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role->id)],
            'permissions' => ['array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'name')],
        ]);

        // Nama role inti dikunci; role kustom boleh di-rename.
        if (! $isProtected) {
            $role->name = Str::of($validated['name'])->lower()->slug('_')->value();
            $role->save();
        }

        // Role admin selalu memegang semua izin — tidak bisa dilucuti.
        if ($role->name === 'admin') {
            $role->syncPermissions(Permission::pluck('name')->all());
        } else {
            $role->syncPermissions($validated['permissions'] ?? []);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', "Role \"{$role->name}\" berhasil diperbarui.");
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, self::PROTECTED_ROLES, true)) {
            return back()->with('error', "Role inti \"{$role->name}\" tidak dapat dihapus.");
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', "Role \"{$role->name}\" masih dipakai pengguna. Lepaskan dulu dari pengguna terkait.");
        }

        $name = $role->name;
        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role \"{$name}\" berhasil dihapus.");
    }

    /**
     * Katalog izin dikelompokkan untuk tampilan checkbox.
     *
     * @return array<string, array<string, string>>
     */
    private function permissionGroups(): array
    {
        return RoleAndPermissionSeeder::PERMISSIONS;
    }
}
