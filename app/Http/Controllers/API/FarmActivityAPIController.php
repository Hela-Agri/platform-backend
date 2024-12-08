<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFarmActivityAPIRequest;
use App\Http\Requests\API\UpdateFarmActivityAPIRequest;
use App\Models\FarmActivity;
use App\Models\FarmActivityItem;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\LoanPackage;
use App\Models\Cohort;
use App\Models\Status;
use App\Models\Wallet;
use App\Models\Harvest;
use App\Models\WalletTransaction;
use App\Repositories\FarmActivityRepository;
use App\Repositories\SettingRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDF;
use stdClass;
use Illuminate\Support\Facades\Validator;
/**
 * Class FarmActivityAPIController
 */
class FarmActivityAPIController extends AppBaseController
{
    private FarmActivityRepository $farmActivityRepository;
    private SettingRepository $settingRepository;

    public function __construct(FarmActivityRepository $farmActivityRepo, SettingRepository $settingRepo)
    {
        $this->settingRepository = $settingRepo;
        $this->farmActivityRepository = $farmActivityRepo;
    }

    /**
     * Display a listing of the FarmActivities.
     * GET|HEAD /farm-activities
     * @throws \Exception
     */
    public function index(Request $request): JsonResponse
    {

        $farmActivities = $this->farmActivityRepository
            ->with(['farm', 'cohort', 'status', 'offtaker'])
            ->withSum('activityItems', 'total')
            ->when($request->has('farmer_id'), function ($query) use ($request) {
                return $query->where('user_id', $request->farmer_id);
            })
            ->when($request->has('off_taker_id'), function ($query) use ($request) {
                return $query->whereHas('offtaker', function ($query2) use ($request) {
                    return $query2->where('users.id', $request->off_taker_id);
                });
            })
            ->when($request->has('cohort_id'), function ($query) use ($request) {
                return $query->where('cohort_id', $request->cohort_id);
            })

            ->orderBy('created_at', 'desc')
            ->paginate($request->get('limit', 50));

        return $this->sendResponse($farmActivities->toArray(), 'Farm Activities retrieved successfully');
    }

    /**
     * Store a newly created FarmActivity in storage.
     * POST /farm-activities
     */
    public function store(CreateFarmActivityAPIRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            if ($request->has('farm_activities')) {
                foreach ($request->get('farm_activities') as $farm_activity) {

                    $input = $request->all();

                    $wallet = Wallet::where('user_id', $farm_activity['off_taker_id'])->first();

                    if ($wallet) {
                        $cohort = Cohort::find($farm_activity['cohort_id']);
                        $start_date = Carbon::now();
                        $end_date = $start_date->addDays($cohort->duration);

                        $input['cohort_id'] = $farm_activity['cohort_id'];
                        $input['user_id'] = $farm_activity['user_id'];
                        $input['farm_id'] = $farm_activity['farm_id'];
                        $input['loan_package_id'] = $farm_activity['load_product_id'];
                        $input['start_date'] = $start_date;
                        $input['end_date'] = $end_date;
                        $input['wallet_id'] = $wallet->id;
                        $input['status_id'] = Status::where('code', 'PENDING')->first()->id;

                        $farmActivity = $this->farmActivityRepository->create($input);

                        if ($request->has('farm_activity_items')) {
                            foreach ($request->get('farm_activity_items') as $item) {
                                if ($item['item_id']) {
                                    FarmActivityItem::updateOrCreate([
                                        'farm_activity_id' => $farmActivity->id,
                                        'rate_card_id' => $item['item_id'],
                                        'quantity' => $item['quantity'],
                                        'total' => $item['total'],
                                        'date' => $item['date'],
                                    ]);
                                }
                            }
                        }
                    } else {
                        return $this->sendError('wallet not found');
                    }
                }
                DB::commit();

                return $this->sendSuccess('Farm Activity saved successfully');
            } else {
                return $this->sendError('Incomplete request');
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * Display the specified FarmActivity.
     * GET|HEAD /farm-activities/{id}
     * @throws \Exception
     */
    public function show($id): JsonResponse
    {
        /** @var FarmActivity $farmActivity */
        $farmActivity = $this->farmActivityRepository
            ->with(['activityItems', 'farm', 'cohort', 'package', 'status', 'siteVisits', 'harvests', 'offtaker'])
            ->withSum('activityItems', 'total')
            ->find($id);

        if (empty($farmActivity)) {
            return $this->sendError('Farm Activity not found');
        }

        return $this->sendResponse($farmActivity->toArray(), 'Farm Activity retrieved successfully');
    }

    /**
     * Update the specified FarmActivity in storage.
     * PUT/PATCH /farm-activities/{id}
     */
    public function update($id, Request $request): JsonResponse
    {

        $input = $request->all();
        /** @var FarmActivity $farmActivity */
        $farmActivity = $this->farmActivityRepository->find($id);

        if (empty($farmActivity)) {
            return $this->sendError('Farm Activity not found');
        }

        $this->farmActivityRepository->update($input, $id);

        if ($request->has('farm_activity_items')) {
            foreach ($request->get('farm_activity_items') as $item) {
                if ($item['item_id']) {
                    FarmActivityItem::updateOrCreate(
                        [
                            'id' => $item['id'],
                        ],
                        [
                            'farm_activity_id' => $id,
                            'rate_card_id' => $item['item_id'],
                            'quantity' => $item['quantity'],
                            'total' => $item['total'],
                            'date' => $item['date'],
                        ]
                    );
                }
            }
        }



        if ($request->has('farm_activity_yields')) {
            foreach ($request->get('farm_activity_yields') as $yield) {
                $yield['farm_activity_id'] = $id;
                $yield['user_id'] = \Auth::user()->id;
                $validator = Validator::make($yield, Harvest::$rules);
                if ($validator->fails()) {
                    // Handle validation failure, such as returning errors to the api
                    return response()->json($validator->errors());
                } else {
                    Harvest::updateOrCreate(
                        [
                            'id' => $yield['id'] ?? '',
                        ],
                        $yield
                    );
                }
            }
        }
        return $this->sendResponse($farmActivity->toArray(), 'FarmActivity updated successfully');
    }
    /**
     * Print the multiple FarmActivity Statement in storage.
     * PUT/PATCH /print_statement
     * VARS uuids
     */
    public function multi_print_statement(Request $request)
    {
        $time = time();

        // Sample data
        /** @var FarmActivity $farmActivity */
        $farmActivities = $this->farmActivityRepository->with(['cohort', 'farm', 'farmer', 'activityItems', 'package', 'harvests'])->whereIn('id', $request->get('uuids', []))->oldest()->get();

        dd($farmActivities);
        /** @var Setting $setting */
        $setting = $this->settingRepository->with(['uploads'])->first();
        $logo = '';
        if ($setting->uploads[0]) {
            $logo = base64_encode(file_get_contents($setting->uploads[0]->path));
        }

        //return view('downloads.input_statement', ['farmActivity'=>$farmActivity,'setting'=>$setting,'logo'=>$logo]);
        // Load view and pass data to it
        $pdf = PDF::loadView('downloads.input_statement', ['farmActivity' => $farmActivity, 'setting' => $setting, 'logo' => $logo]);
        $pdf->setPaper('A4', 'portrait');

        $wm_path = public_path() . '/watermark.jpeg';
        $canvas = $pdf->getCanvas();

        $pageWidth = $canvas->get_width();
        $pageHeight = $canvas->get_height();

        $canvas->set_opacity(0.2);

        $canvas->image(
            $wm_path,
            $pageWidth / 2 - 150,
            $pageHeight / 2 - 50,
            300,
            300
        );

        // Stream or download the PDF
        return $pdf->download('input_statement_' . $time . '.pdf');

    }
    /**
     * Print the specified FarmActivity Statement in storage.
     * PUT/PATCH /print_statement/{id}
     */
    public function print_statement($id, Request $request)
    {
        $time = time();
        // Sample data
        /** @var FarmActivity $farmActivity */
        $farmActivity = $this->farmActivityRepository->with(['cohort', 'farm', 'farmer', 'activityItems', 'package', 'harvests'])->find($id);


        $loan = Loan::with([
            'payments' => function ($query) {
                $query->orderBy('transaction_date', 'asc');
            },
            'loanPayments' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }
        ])->where('farm_activity_id', '=', $farmActivity->id)->first();

        /** @var Setting $setting */
        $setting = $this->settingRepository->with(['uploads'])->first();
        $logo = '';
        if ($setting->uploads[0]) {
            $logo = base64_encode(file_get_contents($setting->uploads[0]->path));
        }

        //return view('downloads.input_statement', ['farmActivity' => $farmActivity, 'loan' => $loan, 'setting' => $setting, 'logo' => $logo]);
        // Load view and pass data to it
        $pdf = PDF::loadView('downloads.input_statement', ['farmActivity' => $farmActivity, 'loan' => $loan, 'setting' => $setting, 'logo' => $logo]);
        $pdf->setPaper('A4', 'portrait');

        $wm_path = public_path() . '/watermark.jpeg';
        $canvas = $pdf->getCanvas();

        $pageWidth = $canvas->get_width();
        $pageHeight = $canvas->get_height();

        $canvas->set_opacity(0.2);

        $canvas->image(
            $wm_path,
            $pageWidth / 2 - 150,
            $pageHeight / 2 - 50,
            300,
            300
        );

        // Stream or download the PDF
        return $pdf->download('input_statement_' . $time . '.pdf');

    }
    public function approveActivityJob($uuid, $date)
    {
        $response = [];
        DB::beginTransaction();
        try {
            $wallet_transaction = new stdClass();

            $farmActivity = FarmActivity::lockForUpdate()
                ->whereDoesntHave('loan')
                ->with(['cohort', 'package'])
                ->find($uuid);

            if (!empty($farmActivity)) {
                $farm_activity_items = FarmActivityItem::where('farm_activity_id', $farmActivity->id)->with('rateCard')->get();
                $wallet = Wallet::find($farmActivity->wallet_id);
                $maturity_date = \Carbon\Carbon::parse($date)->addDays($farmActivity->package->duration)->format('Y-m-d');

                if (!empty($wallet)) {
                    $loan = Loan::create([
                        'sub_total' => 0,
                        'total' => 0,
                        'farm_activity_id' => $farmActivity->id,
                        'user_id' => $farmActivity->user_id,
                        'interest' => 0,
                        'principle_amount' => 0,
                        'processing_fee' => $farmActivity->package->processing_fee ?? 0,
                        'approval_date' => $date,
                        'maturity_date' => $maturity_date

                    ]);
                    $sub_total = 0;
                    foreach ($farm_activity_items as $item) {

                        LoanItem::create([
                            'farm_activity_item_id' => $item->id,
                            'item_id' => $item->rateCard->item_id,
                            'amount' => $item->total,
                            'balance' => $item->total,
                            'loan_id' => $loan->id,
                            'created_at' => $date
                        ]);
                        $sub_total += $item->total;
                    }

                    if ($wallet->balance >= $sub_total) {
                        $wallet_transaction = WalletTransaction::create([
                            'wallet_id' => $wallet->id,
                            'user_id' => $wallet->user_id,
                            'amount' => $sub_total,
                            'type' => 'debit',
                            'created_at' => $date
                        ]);

                        // Less wallet amount
                        $wallet->balance -= $sub_total;
                        $wallet->save();
                    } else {
                        DB::rollBack();
                        // Alert user of less funds
                        $response[$uuid]['success'] = false;
                        $response[$uuid]['message'] = 'Insufficient funds to complete transaction';
                        Log::critical($response);
                        throw ('Insufficient funds to complete transaction');
                    }

                    if (isset($wallet_transaction->id)) {

                        $interest = $this->loanCalculator($sub_total, $farmActivity);
                        $total = $sub_total + $interest + $farmActivity->package->processing_fee;

                        $loan->update([
                            'wallet_transaction_id' => $wallet_transaction->id,
                            'interest' => $interest,
                            'sub_total' => $sub_total,
                            'total' => $total,
                            'balance' => $total,
                        ]);

                        $farmActivity->update([
                            'status_id' => Status::where('code', 'APPROVED')->first()->id
                        ]);


                        DB::commit();

                        $response[$uuid]['success'] = true;
                        $response[$uuid]['message'] = 'Farm Activity approved successfully';
                        Log::critical($response);

                    } else {
                        $response[$uuid]['success'] = true;
                        $response[$uuid]['message'] = 'Faile creating wallet transaction';
                        Log::critical($response);
                        DB::rollBack();
                    }
                } else {
                    $response[$uuid]['success'] = false;
                    $response[$uuid]['message'] = 'Wallet not found';
                    Log::critical($response);
                    DB::rollBack();
                }
            } else {
                $response[$uuid]['success'] = false;
                $response[$uuid]['message'] = 'Farm Activity not found';
                Log::critical($response);
                DB::rollBack();
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            $response[$uuid]['success'] = false;
            $response[$uuid]['message'] = $exception->getMessage();
            Log::critical($response);
        }
    }
    public function approveFarmActivities(Request $request)
    {
        $date = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        if ($request->has('approval_date'))
            $date = \Carbon\Carbon::parse($request->get('approval_date'))->format('Y-m-d H:i:s');

        if ($request->has('uuids')) {
            foreach ($request->get('uuids') as $uuid) {
                $this->approveActivityJob($uuid, $date);
            }
        }
        return $this->sendSuccess('Farm Activity approvals processed successfully');
    }

    public function approveFarmActivity($activity_id, Request $request)
    {
        $date = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        if ($request->has('approval_date'))
            $date = \Carbon\Carbon::parse($request->get('approval_date'))->format('Y-m-d H:i:s');
        $this->approveActivityJob($activity_id, $date);
        return $this->sendSuccess('Farm Activity approval processed successfully');
    }

    public function loanCalculator($sub_total, $farmActivity)
    {

        $cohort_duration = $farmActivity->cohort->duration;
        $interest_span = $farmActivity->package->duration;
        $interest_rate = $farmActivity->package->interest_rate;
        $interest_type = $farmActivity->package->rate_type;

        if ($interest_type == 'percentage') {
            $principle_amount = ($cohort_duration / $interest_span) * ($interest_rate / 100) * $sub_total;
        } else {
            $principle_amount = ($cohort_duration / $interest_span) * $sub_total;
        }

        return $principle_amount;

    }

    /**
     * Remove the specified FarmActivity from storage.
     * DELETE /farm-activities/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var FarmActivity $farmActivity */
        $farmActivity = $this->farmActivityRepository->find($id);

        if (empty($farmActivity)) {
            return $this->sendError('Farm Activity not found');
        }

        $farmActivity->delete();

        return $this->sendSuccess('Farm Activity deleted successfully');
    }
}
