<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Relationship;
class RelationshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $relationships = array(
            "Father",
            "Mother",
            "Son",
            "Daughter",
            "Brother",
            "Sister",
            "Uncle",
            "Aunt",
            "GrandFather",
            "GrandMother",
            "Guardian",
            "Spouse"
        );
        foreach ($relationships as $relationship) {
            try{
                Relationship::updateOrCreate(
                    array(
                        'name' => $relationship,
                    )
                );
            }catch(\Exception $e){
                \Log::critical($e);
            }
        }
    }
}
