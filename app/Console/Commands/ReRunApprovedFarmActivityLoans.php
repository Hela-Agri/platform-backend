<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;


use App\Models\FarmActivity;
class ReRunApprovedFarmActivityLoans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:re-run-approved-farm-activity-loans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
         // Create an instance of the controller
         $controller = app( \App\Http\Controllers\API\FarmActivityAPIController::class);
         
         FarmActivity::whereHas('status',function($q){
            $q->where('code','APPROVED');
         }) ->chunk(50, function($activities) use($controller) {
            foreach ($activities as $activity){

                $date=\Carbon\Carbon::now()->format('Y-m-d H:i:s');
                $loan_bk=\DB::table('loans_bk')->where('farm_activity_id', $activity->id)->oldest()->first();
                if($loan_bk){
                    $date=\Carbon\Carbon::parse($loan_bk->created_at)->format('Y-m-d H:i:s');
                }
                // Call the method you want to execute
                $controller->approveActivityJob($activity->id,$date);
            }
        });
        
 
         // Output a success message
         $this->info('Controller function executed successfully!');

    }
}
