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

    public function updateRole(Request $request, string $role)
    {
        // Admin ge permissions lock; reader witharai wenas karanna puluwan
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