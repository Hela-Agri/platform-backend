<?php

namespace Database\Seeders;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Redis;
class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        $countries = array(


            array(
                'code' => 'KE',
                'name' => 'Kenya',
                'country_code' => '254',
                'phone_number_length' => 9,
                'administration_level' => 2,
                'administration_level_one_label' => 'County',
                'administration_level_two_label' => 'Sub-County',
            ),
            array(
                'code' => 'UG',
                'name' => 'Uganda',
                'country_code' => '256',
                'phone_number_length' => 9,
                'administration_level' => 2,
                'administration_level_one_label' => 'County',
                'administration_level_two_label' => '',
            ),
            array(
                'code' => 'TZ',
                'name' => 'Tanzania',
                'country_code' => '255',
                'phone_number_length' => 9,
                'administration_level' => 2,
                'administration_level_one_label' => 'County',
                'administration_level_two_label' => '',
            ),
            array(
                'code' => 'SO',
                'name' => 'Somalia',
                'country_code' => '252',
                'phone_number_length' => 9,
                'administration_level' => 2,
                'administration_level_one_label' => 'County',
                'administration_level_two_label' => '',
            ),
            array(
                'code' => 'SS',
                'name' => 'Sudan',
                'country_code' => '249',
                'phone_number_length' => 9,
                'administration_level' => 2,
                'administration_level_one_label' => 'County',
                'administration_level_two_label' => '',
            ),
            array(
                'code' => 'SD',
                'name' => 'Sudan',
                'country_code' => '211',
                'phone_number_length' => 9,
                'administration_level' => 2,
                'administration_level_one_label' => 'County',
                'administration_level_two_label' => '',
            ),
            array(
                'code' => 'GH',
                'name' => 'Ghana',
                'country_code' => '233',
                'phone_number_length' => 9,
                'administration_level' => 2,
                'administration_level_one_label' => 'County',
                'administration_level_two_label' => '',
            ),
            array(
                'code' => 'NG',
                'name' => 'Nigeria',
                'country_code' => '234',
                'phone_number_length' => 9,
                'administration_level' => 2,
                'administration_level_one_label' => 'State',
                'administration_level_two_label' => '',
            ),
            array(
                'code' => 'ZM',
                'name' => 'Zambia',
                'country_code' => '260',
                'phone_number_length' => 9,
                'administration_level' => 2,
                'administration_level_one_label' => 'County',
                'administration_level_two_label' => '',
            ),
            array(
                'code' => 'ZA',
                'name' => 'South Africa',
                'country_code' => '27',
                'phone_number_length' => 9,
                'administration_level' => 2,
                'administration_level_one_label' => 'County',
                'administration_level_two_label' => '',
            )
        );

        \Schema::disableForeignKeyConstraints();
        foreach ($countries as $country) {
           try{
                $country=Country::updateOrCreate(
                    array(
                        'country_code' => $country['country_code'],
                    ), array(
                        'code' => $country['code'],
                        'name' => $country['name'],
                        'country_code' => $country['country_code'],
                        'phone_number_length' => $country['phone_number_length'],
                        'administration_level' => $country['administration_level'],
                        'administration_level_one_label' => $country['administration_level_one_label'],
                        'administration_level_two_label' => $country['administration_level_two_label'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    )

                );


           }catch(\Exception $e){
            \Log::critical($e);
           }

        }
        \Schema::enableForeignKeyConstraints();


    }
}
