<?php

namespace Database\Seeders;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = array(
            "BIO Fertilizer",
            "Herbicides",
            "Pesticides",
            "Seeds",
            "Insecticide",
            "Fungicide",
        );
        foreach ($categories as $category) {
            try{
                Category::updateOrCreate(
                    array(
                        'name' => $category,
                        'description' => $category,
                    )
                );
            }catch(\Exception $e){
                \Log::critical($e);
            }
        }
    }
}
