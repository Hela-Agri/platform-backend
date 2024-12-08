<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $status = DB::table('statuses')->where('name', 'Active')->first();
        $role= DB::table('roles')->where('code', 'super_administrator')->first();
        Schema::disableForeignKeyConstraints();

        try{
            User::updateOrCreate(
                array(
                    'username' => 'super.admin',
                    'email' => 'super.admin@mail.com',
                    'phone_number' => '+2547272970853',
                ),
                array(
                    'first_name' => 'Super',
                    'last_name' => 'Admin',
                    'code' => '001',
                    'username' => 'super.admin',
                    'email' => 'super.admin@mail.com',
                    'phone_number' => '+2547272970853',
                    'registration_number' => '123456789',
                    'email_verified_at' => now(),
                    'password' => Hash::make('admin123'),
                    'role_id' => $role->id,
                    'status_id' => $status->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                )
            );
        }catch(\Exception $e){
            \Log::critical($e);
        }
        Schema::enableForeignKeyConstraints();
    }
}
