<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['posts.create',       'Create posts',          'Posts'],
            ['posts.edit',         'Edit own posts',        'Posts'],
            ['posts.delete',       'Move posts to trash',   'Posts'],
            ['comments.create',    'Write comments',        'Comments'],
            ['comments.moderate',  'Delete any comment',    'Comments'],
            ['posts.like',         'Like posts',            'Engagement'],
            ['posts.bookmark',     'Save posts',            'Engagement'],
            ['dashboard.view',     'View dashboard',        'Admin area'],
            ['categories.manage',  'Manage categories',     'Admin area'],
            ['analytics.view',     'View analytics',        'Admin area'],
            ['smtp.manage',        'Manage SMTP settings',  'Admin area'],
            ['users.manage',       'Manage users',          'Admin area'],
            ['permissions.manage', 'Manage permissions',    'Admin area'],
        ];

        foreach ($permissions as [$name, $label, $group]) {
            Permission::updateOrCreate(['name' => $name], ['label' => $label, 'group' => $group]);
        }

        // Reader ge default permissions (palaweni parak witharai)
        if (! DB::table('role_permissions')->where('role', 'reader')->exists()) {
            $ids = Permission::whereIn('name', ['comments.create', 'posts.like', 'posts.bookmark'])->pluck('id');

            foreach ($ids as $id) {
                DB::table('role_permissions')->insert([
                    'role'          => 'reader',
                    'permission_id' => $id,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }

        Cache::forget('role_permissions.reader');
    }
}