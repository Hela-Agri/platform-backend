<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = array(
            array(
                'name' => 'Super Admin',
                'code' => 'super_administrator',
                'system' => true,
                'description' => 'Super Administrator',
            ),
            array(
                'name' => 'Administrator',
                'code' => 'administrator',
                'system' => true,
                'description' => 'Administrator',
            ),
            array(
                'name' => 'Farmer',
                'code' => 'farmer',
                'system' => true,
                'visible' => false,
                'description' => 'Farmer',
            ),
            array(
                'name' => 'Accountant',
                'code' => 'accountant',
                'description' => 'Accountant',
            ),
            array(
                'name' => 'Creditor',
                'code' => 'creditor',
                'description' => 'Creditor',
            ),
            array(
                'name' => 'Field Officer',
                'code' => 'field_officer',
                'description' => 'Field Officer',
            ),
            array(
                'name' => 'Off Taker',
                'code' => 'off_taker',
                'system' => true,
                'visible' => true,
                'description' => 'Individuals who purchase farm produce from the farmers',
            ),
            array(
                'name' => 'Center Officer',
                'code' => 'center_officer',
                'system' => true,
                'visible' => true,
                'description' => 'Individuals who are charged with overseeing farmer activities within a given Center',
            )
        );

        foreach ($roles as $role) {
            try{
                Role::updateOrCreate(
                    array(
                        'name' => $role['name'],
                    ),
                    array(
                        'name' => $role['name'],
                        'code' => $role['code'],
                        'system' => $role['system']??false,
                        'visible' => $role['visible']??true,
                        'description' => $role['description'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    )
                );
            }catch(\Exception $e){
                \Log::critical($e);
            }
        }
    }
}
