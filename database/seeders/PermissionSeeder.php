<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->reservation_permissions();
        // $this->asign_roles();
        $this->asign_chas();
    }
    private function reservation_permissions() {
        Permission::create(['name' => 'create reservations']);
        Permission::create(['name' => 'edit reservations']);
        Permission::create(['name' => 'delete reservations']);

        $admin = Role::findByName('admin');
        $manager = Role::findByName('manager');
        $user = Role::findByName('user');

        $admin->givePermissionTo(['create reservations', 'edit reservations', 'delete reservations']);
        $manager->givePermissionTo('create reservations', 'edit reservations');
        $user->givePermissionTo('create reservations');
    }
    private function asign_roles() {
        $users = User::all();
        $admin = User::find(4);
        $admin->assignRole('admin');
        foreach ($users as $user) {
            $user->assignRole('user');
        }
    }
    private function asign_chas() {
        $user = User::find(7);
        $user->assignRole('user');
    }
}
