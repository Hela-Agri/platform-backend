<?php

namespace Database\Seeders;
use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = array(
            ['name'=>"Square Meter (m²)",'classification'=>'farm'],
            ['name'=>"Square Kilometer (K²)",'ratio'=>'247.105','classification'=>'farm'],
            ['name'=>"Acre",'ratio'=>'1','classification'=>'farm'],
            ['name'=>"Hectare",'ratio'=>'2.4710','classification'=>'farm'],
            ['name'=>"Square Foot (ft²)",'ratio'=>'0.00002295684','classification'=>'farm'],
            ['name'=>"Square Yard (yd²)",'ratio'=>'0.000206612','classification'=>'farm'],
            ['name'=>"Square Mile (mi²)",'ratio'=>'640','classification'=>'farm'],
            ['name'=>"Are",'ratio'=>'0.0247105','classification'=>'farm'],

            ['name'=>"ML",'ratio'=>'','classification'=>'product'],
            ['name'=>"MG",'ratio'=>'','classification'=>'product'],
            ['name'=>"KG",'ratio'=>'','classification'=>'product'],
            ['name'=>"Ltr",'ratio'=>'','classification'=>'product'],
            ['name'=>"Pieces",'ratio'=>'','classification'=>'product'],
            ['name'=>"Box",'ratio'=>'','classification'=>'product'],
            ['name'=>"Carton",'ratio'=>'','classification'=>'product'],
           
        );
        foreach ($units as $unit) {
            try{
                Unit::updateOrCreate(
                    array(
                        'name' => $unit['name'],
                    ),
                    $unit
                );
            }catch(\Exception $e){
                \Log::critical($e);
            }
        }
    }
}

