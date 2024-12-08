<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Models\Status;
use Illuminate\Console\Command;

class LoanMaturityChecker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:loan-maturity-checker';

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

        $loans = Loan::get();

        foreach ($loans as $loan) {
            $loan->status_id =  Status::where('code','ACTIVE')->first()->id;
            $loan->payment_status_id =  Status::where('code','PENDING')->first()->id;
            $loan->save();
        }

        Loan::where('maturity_date', '<', now())
            ->where('status_id', '!=', Status::where('code','CLOSED')->first()->id)
            ->chunk(100, function($loans) {
                foreach ($loans as $loan) {
                    $loan->status_id = Status::where('code','OVERDUE')->first()->id;
                    $loan-> save();
                }
            });
    }
}
