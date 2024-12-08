<?php

namespace App\Http\Controllers\API;
use App\Exports\AccountingReportExport;
use App\Exports\CohortLoanReport;
use App\Exports\FarmerLoanReport;
use App\Exports\WalletStatement;
use App\Exports\YieldReport;
use App\Exports\LoanReport;
use App\Models\Cohort;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\User;
use App\Repositories\FarmActivityRepository;
use App\Repositories\SettingRepository;
use App\Repositories\WalletTransactionRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Farmer;
use App\Models\Farm;
use App\Models\Harvest;
use App\Models\FarmActivityItem;
use App\Models\FarmActivity;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;
/**
 * Class CenterAPIController
 */
class ReportAPIController extends AppBaseController
{

    private FarmActivityRepository $farmActivityRepository;
    private SettingRepository $settingRepository;
    private WalletTransactionRepository $walletTransactionRepository;

    public function __construct(
        FarmActivityRepository $farmActivityRepo,
        WalletTransactionRepository $walletTransactionRepo,
        SettingRepository $settingRepo
    ) {
        $this->settingRepository = $settingRepo;
        $this->farmActivityRepository = $farmActivityRepo;
        $this->walletTransactionRepository = $walletTransactionRepo;
    }
    public function statistics(Request $request): JsonResponse
    {
        $data = array();
        $farm_activity_total = FarmActivity::join('farm_activity_items', 'farm_activities.id', '=', 'farm_activity_items.farm_activity_id')
            ->sum('farm_activity_items.total');

        $loan_sub_total = Loan::sum('sub_total');
        $data['total_farmers'] = Farmer::count();
        $data['total_farms'] = Farm::count();
        $data['total_acres'] = Farm::sum('acres');
        $data['farm_input_total'] = number_format($loan_sub_total);
        $data['average_acres'] = number_format($data['total_acres'] / $data['total_farmers'], 2);
        $data['average_farm_input'] = number_format($loan_sub_total / $data['total_farmers'], 2);

        return $this->sendResponse($data, 'Statistics retrieved successfully');
    }
    public function yieldsReport(Request $request)
    {

        try {
            $data = [];
            $now = Carbon::now();
            $from = $now->startOfyear()->toDateString();
            $to = $now->endOfYear()->toDateString();

            if (isset($request->start_date) && !empty($request->start_date)) {
                $from = $request->start_date;
            }

            if (isset($request->end_date) && !empty($request->end_date)) {
                $to = $request->end_date;
            }


            $farmActivities = FarmActivity::with(['farmer', 'farm', 'cohort'])
                ->when($request->has('farmer_id') && $request->get('farmer_id'), function ($query) use ($request) {
                    $query->where('user_id', $request->get('farmer_id'));
                })
                ->when($request->has('cohort_id') && $request->get('cohort_id'), function ($query) use ($request) {
                    $query->where('cohort_id', $request->get('cohort_id'));
                })
                ->when($request->has('center_id') && $request->get('center_id'), function ($query) use ($request) {
                    $query->whereHas('center', function ($q) use ($request) {
                        $q->where('centers.id', $request->get('center_id'));
                    });
                })
                ->whereHas('harvests', function ($q) use ($from, $to) {
                    $q->whereBetween('harvest_date', [$from, $to])->orderBy('harvests.created_at', 'desc');
                })
                ->withSum('harvests', 'weight')
                ->withCount('harvests')
                ->paginate($request->get('limit', 50));



            $data = $farmActivities->toArray();
            $data['total_weight'] = number_format($farmActivities->sum('harvests_sum_weight'), 2);
            $data['total_counts'] = number_format($farmActivities->sum('harvests_count'), 2);


            return $this->sendResponse($data, 'Yields retrieved successfully');
        } catch (\Exception $e) {

            Log::error('Export error: ' . $e->getMessage());
            return $this->sendResponse($data, 'Yields retrieved successfully');
        }
    }

    public function loansReport(Request $request) //: JsonResponse
    {
        try {
            $now = Carbon::now();
            $from = $now->startOfyear()->toDateString();
            $to = $now->endOfYear()->toDateString();

            if (!empty($request->start_date)) {
                $from = $request->start_date;
            }

            if (!empty($request->end_date)) {
                $to = $request->end_date;
            }
            // $loans = Loan::get();
            // foreach ($loans as $loan) {

            //     $loan->balance = $loan->total;
            //     $loan->save();
            // }
            $baseQuery = Loan::with(['farmer', 'farm_activity', 'status', 'payment_status'])
                ->whereBetween('approval_date', [$from, $to])
                ->when($request->filled('farmer_id'), fn($query) => $query->where('user_id', $request->farmer_id))
                ->when($request->filled('status_id'), fn($query) => $query->where('status_id', $request->status_id))
                ->when($request->filled('cohort_id'), function ($query) use ($request) {
                    $query->whereHas('farm_activity.cohort', fn($q) => $q->where('id', $request->cohort_id));
                })
                ->when($request->filled('center_id'), function ($query) use ($request) {
                    $query->whereHas('farm_activity.cohort.center', fn($q) => $q->where('id', $request->center_id));
                });

            $totalsQuery = clone $baseQuery;

            $loans = $baseQuery->withSum('loanPayments', 'amount')->orderBy('created_at', 'DESC')
                ->paginate($request->get('limit', 50));

            $totals = $totalsQuery->get([
                DB::raw('SUM(sub_total) as total_principle'),
                DB::raw('SUM(processing_fee) as total_processing_fee'),
                DB::raw('SUM(interest) as total_interest'),
                DB::raw('SUM(total) as total_loan'),
                DB::raw('SUM(balance) as total_balance'),
            ])->first();

            $totalPayments = $baseQuery->get()->sum(function ($loan) {
                return $loan->loan_payments_sum_amount;
            });

            $data = $loans->toArray();
            $data['total_principle'] = number_format($totals->total_principle, 2);
            $data['total_processing_fee'] = number_format($totals->total_processing_fee, 2);
            $data['total_interest'] = number_format($totals->total_interest, 2);
            $data['total_loan'] = number_format($totals->total_loan, 2);
            $data['balance_total'] = number_format($totals->total_balance, 2);
            //            $data['paid_total'] = number_format($loans->sum('loan_payments_sum_amount'), 2);
            $data['paid_total'] = number_format($totalPayments, 2);


            return $this->sendResponse($data, 'Loans retrieved successfully');

        } catch (\Exception $e) {
            dd($e);
            Log::error('Export error: ' . $e->getMessage());
            return $this->sendError('Error occured while retrieving loans');
        }
    }

    public function downloadYieldReportExcel(Request $request)
    {
        try {
            $timestamp = time();

            if (isset($request->date_range) && !empty($request->date_range[0]) && !empty($request->date_range[1])) {
                $from = $request->date_range[0];
                $to = $request->date_range[1];
            } else {
                $now = Carbon::now();
                $from = $now->startOfyear()->toDateString();
                $to = $now->endOfYear()->toDateString();
            }



            $farmActivities = FarmActivity::with(['farmer', 'farm', 'cohort'])
                ->when($request->has('farmer_id') && $request->get('farmer_id'), function ($query) use ($request) {
                    $query->where('user_id', $request->get('farmer_id'));
                })
                ->when($request->has('cohort_id') && $request->get('cohort_id'), function ($query) use ($request) {
                    $query->where('cohort_id', $request->get('cohort_id'));
                })
                ->when($request->has('center_id') && $request->get('center_id'), function ($query) use ($request) {
                    $query->whereHas('center', function ($q) use ($request) {
                        $q->where('centers.id', $request->get('center_id'));
                    });
                })
                ->whereHas('harvests', function ($q) use ($from, $to) {
                    $q->whereBetween('created_at', [$from, $to])->orderBy('harvests.created_at', 'desc');
                })
                ->withSum('harvests', 'weight')
                ->whereBetween('created_at', [$from, $to])
                ->get();

            $data = [];

            foreach ($farmActivities as $index => $farm_activity) {

                $data[$index]['farmer'] = $farm_activity->farmer->first_name . ' ' . $farm_activity->farmer->middle_name . ' ' . $farm_activity->farmer->last_name;
                $data[$index]['contact'] = $farm_activity->farmer->phone_number;
                $data[$index]['farm'] = $farm_activity->farm->location;
                $data[$index]['center'] = $farm_activity->cohort->center->name;
                $data[$index]['cohort'] = $farm_activity->cohort->name;
                $data[$index]['date'] = \Carbon\Carbon::parse($farm_activity->created_at)->format('d-m-Y');
                $data[$index]['yield'] = $farm_activity->harvests_sum_weight ?? 0;
                $data[$index]['formatted_yield'] = number_format($farm_activity->harvests_sum_weight ?? 0, 2);
            }

            $eloquentCollection = collect($data)->map(function ($item) {
                return (object) $item;
            });
            $eloquentCollection = new Collection($eloquentCollection);

            $title = 'Farm Yield Report';

            $setting = $this->settingRepository->with(['uploads'])->first();
            $logo = '';
            if ($setting->uploads[0]) {
                $logo = $setting->uploads[0];
            }


            $letter_head = 'Period : ' . \Carbon\Carbon::parse($from)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($to)->format('d/m/Y');

            return Excel::download(new YieldReport($eloquentCollection, $logo, $letter_head, $title), 'yield_report_' . $timestamp . '.xlsx');
        } catch (\Exception $e) {

            Log::error('Export error: ' . $e->getMessage());
            return response()->json(['error' => 'Export failed.'], 500);
        }

    }

    public function downloadLoanReportExcel(Request $request)
    {
        try {
            $data = [];

            $timestamp = time();

            if (isset($request->date_range) && !empty($request->date_range[0]) && !empty($request->date_range[1])) {
                $from = $request->date_range[0];
                $to = $request->date_range[1];
            } else {
                $now = Carbon::now();
                $from = $now->startOfyear()->toDateString();
                $to = $now->endOfYear()->toDateString();
            }

            $loans = Loan::with(['farmer', 'farm_activity'])
                ->whereBetween('approval_date', [$from, $to])
                ->orderBy('created_at', 'DESC')
                ->when($request->has('farmer_id') && $request->get('farmer_id'), function ($query) use ($request) {
                    $query->where('user_id', $request->get('farmer_id'));
                })
                ->when($request->has('cohort_id') && $request->get('cohort_id'), function ($query) use ($request) {
                    $query->whereHas('farm_activity.cohort', function ($q) use ($request) {
                        $q->where('id', $request->get('cohort_id'));
                    });
                })
                ->when($request->has('center_id') && $request->get('center_id'), function ($query) use ($request) {
                    $query->whereHas('farm_activity.cohort.center', function ($q) use ($request) {
                        $q->where('id', $request->get('center_id'));
                    });
                })
                ->whereBetween('created_at', [$from, $to])
                ->get();



            foreach ($loans as $index => $loan) {

                $data[$index]['farmer'] = $loan->farmer->first_name . ' ' . $loan->farmer->middle_name . ' ' . $loan->farmer->last_name;
                $data[$index]['contact'] = $loan->farmer->phone_number;
                $data[$index]['farm'] = $loan->farm_activity->farm->location;
                $data[$index]['center'] = $loan->farm_activity->cohort->center->name;
                $data[$index]['cohort'] = $loan->farm_activity->cohort->name;
                $data[$index]['date'] = \Carbon\Carbon::parse($loan->created_at)->format('d-m-Y');
                $data[$index]['sub_total'] = $loan->sub_total;
                $data[$index]['processing_fee'] = $loan->processing_fee;
                $data[$index]['interest'] = $loan->interest;
                $data[$index]['total'] = $loan->total;
            }


            $eloquentCollection = collect($data)->map(function ($item) {
                return (object) $item;
            });
            $eloquentCollection = new Collection($eloquentCollection);

            $title = 'Farm Loan Report';

            $setting = $this->settingRepository->with(['uploads'])->first();
            $logo = '';
            if ($setting->uploads[0]) {
                $logo = $setting->uploads[0];
            }

            $letter_head = 'Period : ' . \Carbon\Carbon::parse($from)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($to)->format('d/m/Y');


            return Excel::download(new LoanReport($eloquentCollection, $logo, $letter_head, $title), 'loan_report_' . $timestamp . '.xlsx');
        } catch (\Exception $e) {

            Log::error('Export error: ' . $e->getMessage());
            return response()->json(['error' => 'Export failed.'], 500);
        }

    }

    public function farmersLoan(Request $request, $id): \Symfony\Component\HttpFoundation\BinaryFileResponse|JsonResponse
    {
        try {
            $timestamp = time();

            if (isset($request->date_range) && !empty($request->date_range[0]) && !empty($request->date_range[1])) {
                $from = $request->date_range[0];
                $to = $request->date_range[1];
            } else {
                $now = Carbon::now();
                $from = $now->startOfyear()->toDateString();
                $to = $now->endOfYear()->toDateString();
            }
            $farmer = User::where('id', $id)->first();

            $loans = Loan::with(['items'])
                ->where('user_id', $id)
                ->whereBetween('created_at', [$from, $to])
                ->orderBy('created_at', 'DESC')
                ->get();


            $aggregated_loans = $loans->groupBy(function ($loan) {
                return $loan->created_at->format('jS M Y');
            })
                ->mapWithKeys(function ($group, $date) {
                    $totalAmount = 0;
                    $rows = [];

                    foreach ($group as $record) {
                        foreach ($record->items as $item) {
                            $totalAmount += $item->amount;

                            $rows[] = [
                                'product_name' => $item->farm_activity_item->rateCard->product->name ?? $item->farm_activity_item->rateCard->service->name,
                                'category_name' => $item->farm_activity_item->rateCard->product->category->name ?? '',
                                'quantity' => number_format(floatval($item->farm_activity_item->quantity) ?? 0, 2),
                                'rate' => number_format(floatval($item->farm_activity_item->rateCard->amount) ?? 0, 2),
                                'amount' => number_format(floatval($item->amount) ?? 0, 2),
                            ];
                        }
                    }

                    return [
                        $date => [
                            'amount' => number_format($totalAmount, 2),
                            'items' => $rows,
                        ],
                    ];
                });

            $aggregated_loans_array = $aggregated_loans->toArray();

            $eloquentCollection = collect($aggregated_loans_array)->map(function ($item) {
                return (object) $item;
            });

            $eloquentCollection = new Collection($eloquentCollection);

            $title = $farmer->first_name . ' ' . 'Loan Report';

            $setting = $this->settingRepository->with(['uploads'])->first();
            $logo = '';
            if ($setting->uploads[0]) {
                $logo = $setting->uploads[0];
            }

            $letter_head = 'Name : ' . $farmer->first_name . ' ' . $farmer->middle_name . ' ' . $farmer->last_name . ', Phone Number : ' . $farmer->phone_number . ', Registration Number : ' . $farmer->registration_number . ', Date Range : ' . \Carbon\Carbon::parse($from)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($to)->format('d/m/Y');
            $formatted_letter_head = str_replace(", ", ",\n", $letter_head);

            return Excel::download(new FarmerLoanReport($eloquentCollection, $logo, $formatted_letter_head, $title), 'farmer_loan_' . $timestamp . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return response()->json(['error' => 'Export failed.'], 500);
        }
    }

    public function cohortLoan(Request $request, $id): \Symfony\Component\HttpFoundation\BinaryFileResponse|JsonResponse
    {
        try {
            $timestamp = time();

            if (isset($request->date_range) && !empty($request->date_range[0]) && !empty($request->date_range[1])) {
                $from = $request->date_range[0];
                $to = $request->date_range[1];
            } else {
                $now = Carbon::now();
                $from = $now->startOfMonth()->toDateString();
                $to = $now->endOfMonth()->toDateString();
            }

            $cohort = Cohort::where('id', $id)->first();

            $farmers_loans = Loan::with(['items', 'farmer'])
                ->whereBetween('created_at', [$from, $to])
                ->whereHas('items.farm_activity_item.farm_activity.cohort', function ($query) use ($id) {
                    $query->where('id', $id);
                })
                ->orderBy('created_at', 'DESC')
                ->get();

            $aggregated_loans = $farmers_loans->groupBy('farmer.id')->map(function ($group) {
                $firstLoan = $group->first();

                return [
                    'farmer_name' => $group->first()->farmer->first_name . ' ' . $group->first()->farmer->middle_name . ' ' . $group->first()->farmer->last_name,
                    'total_sum' => $group->sum('total'),
                    'sub_total_sum' => $group->sum('sub_total'),
                    'interest_sum' => $group->sum('interest'),
                    'interest_rate' => $firstLoan->items->first()->farm_activity_item->farm_activity->package->interest_rate,
                    'processing_fee' => $firstLoan->items->first()->farm_activity_item->farm_activity->package->processing_fee,
                ];
            })->values();

            $aggregated_loans_array = $aggregated_loans->toArray();

            $eloquentCollection = collect($aggregated_loans_array)->map(function ($item) {
                return (object) $item;
            });

            $eloquentCollection = new Collection($eloquentCollection);
            $setting = $this->settingRepository->with(['uploads'])->first();
            $logo = '';
            if ($setting->uploads[0]) {
                $logo = $setting->uploads[0];
            }
            $letter_head = 'Name : ' . $cohort->name . ', Date Range : ' . \Carbon\Carbon::parse($from)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($to)->format('d/m/Y');
            $formatted_letter_head = str_replace(", ", ",\n", $letter_head);

            return Excel::download(new CohortLoanReport($eloquentCollection, $logo, $formatted_letter_head), 'cohort_loan_' . $timestamp . '.xlsx');

        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return response()->json(['error' => 'Export failed.'], 500);
        }
    }

    public function walletStatement(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse|JsonResponse
    {
        try {
            $timestamp = time();
            $now = Carbon::now();
            $from = $now->startOfMonth()->toDateString();
            $to = $now->endOfMonth()->toDateString();

            if (isset($request->start_date) && !empty($request->start_date)) {
                $from = $request->start_date;
            }

            if (isset($request->end_date) && !empty($request->end_date)) {
                $to = $request->end_date;
            }

            $walletTransactions = $this->walletTransactionRepository
                ->with(['wallet.offTaker'])
                ->when($request->has('off_taker_id'), function ($query) use ($request) {
                    $query->whereHas('wallet', function ($q) use ($request) {
                        $q->where('user_id', $request->get('off_taker_id'));
                    });
                })
                ->whereBetween('created_at', [$from, $to])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($transaction) {
                    return [
                        'debit' => $transaction->type === 'debit' ? $transaction->amount : 0,
                        'credit' => $transaction->type === 'credit' ? $transaction->amount : 0,
                        'balance' => $transaction->wallet->balance,
                        'off_taker' => trim(
                            implode(' ', [
                                $transaction->wallet->offTaker->first_name ?? '',
                                $transaction->wallet->offTaker->middle_name ?? '',
                                $transaction->wallet->offTaker->last_name ?? ''
                            ])
                        ),
                        'created_at' => \Carbon\Carbon::parse($transaction->created_at)->format('d/m/Y'),
                    ];
                });

            $totalDebit = $walletTransactions->sum('debit');
            $totalCredit = $walletTransactions->sum('credit');
            $totalBalance = $walletTransactions->groupBy('off_taker')
                ->map(function ($group) {
                    $lastTransaction = collect($group)->sortByDesc('created_at')->first();
                    return $lastTransaction['balance'];
                })
                ->sum();

            $data = [
                'transactions' => $walletTransactions,
                'totals' => [
                    'debit' => round($totalDebit, 1),
                    'credit' => round($totalCredit, 1),
                    'balance' => round($totalBalance, 1),
                ],
            ];

            $eloquentCollection = collect($data)->map(function ($item) {
                return (object) $item;
            });
            $eloquentCollection = new Collection($eloquentCollection);

            $title = 'Wallet Statement Report';

            $setting = $this->settingRepository->with(['uploads'])->first();
            $logo = '';
            if ($setting->uploads[0]) {
                $logo = $setting->uploads[0];
            }

            $letter_head = 'Period : ' . \Carbon\Carbon::parse($from)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($to)->format('d/m/Y');

            return Excel::download(new WalletStatement($eloquentCollection, $logo, $letter_head, $title), 'wallet_statement_' . $timestamp . '.xlsx');

        } catch (\Exception $exception) {
            Log::error('Export error: ' . $exception->getMessage());
            return response()->json(['error' => 'Export failed.'], 500);
        }
    }
}
