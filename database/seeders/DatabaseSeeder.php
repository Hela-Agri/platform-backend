<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(StatusSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(RelationshipSeeder::class);
        $this->call(UnitSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(KenyaAdministrativeLevelsSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(ActionSeeder::class);
        $this->call(PaymentMethodSeeder::class);
        $this->call(CategorySeeder::class);

    }
}
