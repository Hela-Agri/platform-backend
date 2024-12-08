<?php

namespace database\seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;
class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $statuses = array(
            array(
                'code' => 'ACTIVE',
                'name' => 'Active',
            ),
            array(
                'code' => 'DELETED',
                'name' => 'Deleted',
            ),
            array(
                'code' => 'DEACTIVATED',
                'name' => 'Deactivated',
            ),
            array(
                'code' => 'ACTIVATED',
                'name' => 'Activated',
            ),
            array(
                'code' => 'PENDING',
                'name' => 'Pending',
            ),
            array(
                'code' => 'COMPLETE',
                'name' => 'Completed',
            ),
            array(
                'code' => 'REJECTED',
                'name' => 'Rejected',
            ),
            array(
                'code' => 'CONFIRMED',
                'name' => 'Confirmed',
            ),
            array(
                'code' => 'APPROVED',
                'name' => 'Approved',
            ),
            array(
                'code' => 'PROCESSED',
                'name' => 'Processed',
            ),
            array(
                'code' => 'SHIPPED',
                'name' => 'Shipped',
            ),
            array(
                'code' => 'REFUNDED',
                'name' => 'Refunded',
            ),
            array(
                'code' => 'CANCELLED',
                'name' => 'Cancelled',
            ),
            array(
                'code' => 'DECLINED',
                'name' => 'Declined',
            ),
            array(
                'code' => 'AWAITING PICKUP',
                'name' => 'Awaiting Pickup	',
            ),
            array(
                'code' => 'AWAITING SHIPMENT',
                'name' => 'Awaiting Shipment',
            ),
            array(
                'code' => 'AWAITING FULFILMENT',
                'name' => 'Awaiting Fulfillment',
            ),
            array(
                'code' => 'PAID',
                'name' => 'Paid',
            ),
            array(
                'code' => 'PARTIALLY PAID',
                'name' => 'Partially Paid',
            ),
            array(
                'code' => 'AUTHORIZED',
                'name' => 'Authorized',
            ),
            array(
                'code' => 'OVERDUE',
                'name' => 'Overdue',
            ),
            array(
                'code' => 'CLOSED',
                'name' => 'Closed',
            )
        );

        foreach ($statuses as $status) {
            Status::updateOrCreate(
                array(
                    'code' => $status['code'],
                ),array(
                    'code' => $status['code'],
                    'name' => $status['name']
                )
            );

        }
    }
}
