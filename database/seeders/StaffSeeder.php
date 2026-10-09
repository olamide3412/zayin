<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\RoleEnums;
use App\Enums\StatusEnums;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds for Zayin Technologies staff members.
     */
    public function run(): void
    {
        $staffMembers = [
            [
                'name' => 'Zayin Technologies Admin',
                'username' => 'admin',
                'email' => 'admin@zayin.ng',
                'password' => Hash::make('admin123'),
                'role' => RoleEnums::SuperAdministrator->value,
                'status' => StatusEnums::Enable->value,
            ],
            [
                'name' => 'Zayin Lead Engineer',
                'username' => 'engineer',
                'email' => 'engineer@zayin.ng',
                'password' => Hash::make('admin123'),
                'role' => RoleEnums::Staff->value,
                'status' => StatusEnums::Enable->value,
            ],
            [
                'name' => 'Zayin Tech Support',
                'username' => 'support',
                'email' => 'support@zayin.ng',
                'password' => Hash::make('admin123'),
                'role' => RoleEnums::Staff->value,
                'status' => StatusEnums::Enable->value,
            ],
        ];

        foreach ($staffMembers as $data) {
            $user = User::where('username', $data['username'])
                ->orWhere('email', $data['email'])
                ->first() ?? new User();

            $user->fill($data);
            $user->save();
        }
    }
}
