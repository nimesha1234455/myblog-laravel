<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    public const ROLES = ['admin', 'reader'];

    // Admin lock-out wenna puluwan permissions witharai protect karala thiyenne.
    // Anith okkoma (posts.*, comments.*, posts.like ...) delete karanna puluwan.
    public const PROTECTED = [
        'dashboard.view',
        'permissions.manage',
        'users.manage',
    ];

    // Permission Manager page eka
    public function index()
    {
        $permissions = Permission::orderBy('group')->orderBy('id')->get()->groupBy('group');

        $readerIds = DB::table('role_permissions')
            ->where('role', 'reader')
            ->pluck('permission_id')
            ->all();

        $users = User::orderBy('name')->paginate(10);

        return view('permissions.index', [
            'permissions' => $permissions,
            'readerIds'   => $readerIds,
            'users'       => $users,
            'roles'       => self::ROLES,
        ]);
    }

    // Aluth permission ekak add karanawa
    public function store(Request $request)
    {
        $request->merge([
            'name'  => strtolower(trim((string) $request->input('name'))),
            'group' => strtoupper(trim((string) $request->input('group'))),
        ]);

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:100', 'regex:/^[a-z_]+\.[a-z_]+$/', Rule::unique('permissions', 'name')],
            'label' => ['required', 'string', 'max:100'],
            'group' => ['required', 'string', 'max:100'],
        ]);

        Permission::create($data);

        foreach (self::ROLES as $role) {
            Cache::forget("role_permissions.{$role}");
        }

        return redirect()->route('permissions.index')
            ->with('success', "Permission '{$data['name']}' added.");
    }

    // Permission ekak delete karanawa (admin lock-out wena ewa witharak block)
    public function destroy(Permission $permission)
    {
        if (in_array($permission->name, self::PROTECTED, true)) {
            return back()->with('error', "'{$permission->name}' is required for admin access and can't be deleted.");
        }

        DB::transaction(function () use ($permission) {
            DB::table('role_permissions')->where('permission_id', $permission->id)->delete();
            $permission->delete();
        });

        foreach (self::ROLES as $role) {
            Cache::forget("role_permissions.{$role}");
        }

        return back()->with('success', "Permission '{$permission->name}' deleted.");
    }

    // Reader ge permissions update karanawa
    public function updateRole(Request $request, string $role)
    {
        abort_unless($role === 'reader', 403);

        $data = $request->validate([
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $ids = $data['permissions'] ?? [];

        DB::transaction(function () use ($role, $ids) {
            DB::table('role_permissions')->where('role', $role)->delete();

            foreach ($ids as $id) {
                DB::table('role_permissions')->insert([
                    'role'          => $role,
                    'permission_id' => $id,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        });

        Cache::forget("role_permissions.{$role}");

        return back()->with('success', 'Reader permissions updated.');
    }

    // User kenekge role eka wenas karanawa
    public function updateUserRole(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', "You can't change your own role.");
        }

        $data = $request->validate([
            'role' => ['required', Rule::in(self::ROLES)],
        ]);

        $user->forceFill(['role' => $data['role']])->save();

        return back()->with('success', "{$user->name} is now {$data['role']}.");
    }
}