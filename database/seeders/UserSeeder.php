<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\RoleEnums;
use App\Enums\StatusEnums;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clean up legacy hospital accounts if present
        User::where('email', 'like', '%@msehibadan.org')
            ->orWhere('email', 'like', '%@makkaheye.com')
            ->delete();

        // Create or update the primary Administrator account
        $admin = User::where('username', 'admin')
            ->orWhere('email', 'admin@zayin.ng')
            ->first() ?? new User();

        $admin->fill([
            'name' => 'Zayin Technologies Admin',
            'username' => 'admin',
            'email' => 'admin@zayin.ng',
            'password' => Hash::make('admin123'),
            'role' => RoleEnums::SuperAdministrator->value,
            'status' => StatusEnums::Enable->value,
        ]);
        $admin->save();

        // Create or update the Technical Support / Secondary Admin account
        $support = User::where('username', 'support')
            ->orWhere('email', 'support@zayin.ng')
            ->first() ?? new User();

        $support->fill([
            'name' => 'Zayin Tech Support',
            'username' => 'support',
            'email' => 'support@zayin.ng',
            'password' => Hash::make('admin123'),
            'role' => RoleEnums::Administrator->value,
            'status' => StatusEnums::Enable->value,
        ]);
        $support->save();
    }
}
