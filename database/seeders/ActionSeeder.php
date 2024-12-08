<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Action;
class ActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $actions = array(
            ['name'=>"Require Spraying",'description'=>"Require Spraying"],
             ['name'=>"Requires weeding",'description'=>"Requires weeding"],
             ['name'=>"Ready for harvest",'description'=>"Ready for harvest"],
             ['name'=>"Spacing not done properly",'description'=>"Spacing not done properly"],
        );
        foreach ($actions as $action) {
            try{
                Action::updateOrCreate(
                    array(
                        'name' => $action['name'],
                    ),
                    array(
                        'name' => $action['name'],
                        'description' => $action['description'],
                    )
                );
            }catch(\Exception $e){
                \Log::critical($e);
            }
        }
    }
}
