<?php

namespace Database\Seeders;

use App\Models\PaymentMode;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        $payment_methods = array(
            array(
                'name' => 'Cash'
            ),
            array(
                'name' => 'Mpesa'
            ),
            array(
                'name' => 'RTGS'
            ),
            array(
                'name' => 'EFT'
            ),
            array(
                'name' => 'Airtel Money'
            ),
            array(
                'name' => 'Cheque Number'
            ),
        );


        foreach ($payment_methods as $payment_method) {

            PaymentMode::updateOrCreate(
                array(
                    'name' => $payment_method['name'],
                ),
                array(
                    'name' => $payment_method['name'],
                    'description' => $payment_method['name'],
                )
            );
        }
    }
}
