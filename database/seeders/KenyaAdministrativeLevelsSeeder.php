<?php

namespace Database\Seeders;
use App\Models\Country;
use App\Models\AdministrationLevelOne;
use App\Models\AdministrationLevelTwo;
use App\Models\AdministrationLevelThree;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
class KenyaAdministrativeLevelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {

        $file = base_path("database/seeders/data/kenya_administrative_levels.json");

        $counties = json_decode(file_get_contents($file), true);

        $default_country_id=Country::where('code','KE')->first()->id;
        try{
            foreach($counties[0] as $county=>$subcounties){
                $admin_level_one=AdministrationLevelOne::updateOrCreate(
                    array(
                        'name' => $county,
                    ),
                    array(
                        'country_id' => $default_country_id,
                        'name' => $county,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    )
                );


                foreach($subcounties as $sub_county=>$wards){
                    $admin_level_two=AdministrationLevelTwo::updateOrCreate(
                        array(
                            'name' => $sub_county,
                        ),
                        array(
                            'administration_level_one_id' => $admin_level_one->id,
                            'country_id' => $default_country_id,
                            'name' => $sub_county,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        )
                    );


                    foreach($wards as $ward){

                            $admin_level_three=AdministrationLevelThree::updateOrCreate(
                                array(
                                    'name' => $ward,
                                ),
                                array(
                                    'administration_level_two_id' => $admin_level_two->id,
                                    'name' => $ward,
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now()
                                )
                            );


                    }
                }
            }

        }catch(\Exception $e){
            \Log::critical($e);
        }
    }
}
