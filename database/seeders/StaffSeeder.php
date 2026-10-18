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
     * Run the database seeds for Zayin Global Limited staff members.
     */
    public function run(): void
    {
        $staffMembers = [
            [
                'name' => 'Zayin Global Admin',
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
                'name' => 'Zayin Global Support',
                'username' => 'support',
                'email' => 'support@zayin.ng',
                'password' => Hash::make('admin123'),
                'role' => RoleEnums::Staff->value,
                'status' => StatusEnums::Enable->value,
            ],
        ];

        foreach ($staffMembers as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }
    }
}
