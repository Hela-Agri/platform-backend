<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateWalletTransactionAPIRequest;
use App\Http\Requests\API\UpdateWalletTransactionAPIRequest;
use App\Models\WalletTransaction;
use App\Repositories\WalletTransactionRepository;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;

/**
 * Class WalletTransactionAPIController
 */
class WalletTransactionAPIController extends AppBaseController
{
    private WalletTransactionRepository $walletTransactionRepository;

    public function __construct(WalletTransactionRepository $walletTransactionRepo)
    {
        $this->walletTransactionRepository = $walletTransactionRepo;
    }

    /**
     * Display a listing of the WalletTransactions.
     * GET|HEAD /wallet-transactions
     * @throws \Exception
     */
    public function index(Request $request): JsonResponse
    {
        $walletTransactions = $this->walletTransactionRepository
            ->with(['wallet', 'farmer'])
            ->when($request->has('type'), function ($q) use($request) {
                $q->where('type', $request->get('type'));
            })
            ->paginate($request->get('limit', 50));

        return $this->sendResponse($walletTransactions->toArray(), 'Wallet Transactions retrieved successfully');
    }



    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function getWalletStatements(Request $request): JsonResponse
    {

        $now = Carbon::now();
        $from = $now->startOfMonth()->toDateString();
        $to = $now->endOfMonth()->toDateString();

        if (!empty($request->start_date)) {
            $from = $request->start_date;
        }

        if (!empty($request->end_date)) {
            $to = $request->end_date;
        }

        $baseQuery = $this->walletTransactionRepository
            ->with(['wallet.offTaker'])
            ->when($request->filled('off_taker_id'), function ($query) use ($request) {
                $query->whereHas('wallet', function ($q) use ($request) {
                    $q->where('user_id', $request->get('off_taker_id'));
                });
            })
            ->whereBetween('created_at', [$from, $to]);

        $totalsQuery = clone $baseQuery;
        $balanceQuery = clone $baseQuery;

        $walletTransactions = $baseQuery
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('limit', 50));

        $transformedTransactions = $walletTransactions->getCollection()->map(function ($transaction) {
            return [
                'debit' => $transaction->type === 'debit' ? $transaction->amount : 0,
                'credit' => $transaction->type === 'credit' ? $transaction->amount : 0,
                'balance' => $transaction->wallet->balance,
                'off_taker' => trim(implode(' ', [
                    $transaction->wallet->offTaker->first_name ?? '',
                    $transaction->wallet->offTaker->middle_name ?? '',
                    $transaction->wallet->offTaker->last_name ?? ''
                ])),
                'created_at' => $transaction->created_at,
            ];
        });

        $walletTransactions->setCollection($transformedTransactions);

        $totals = $totalsQuery->get([
            DB::raw("SUM(CASE WHEN type = 'debit' THEN amount ELSE 0 END) AS total_debit"),
            DB::raw("SUM(CASE WHEN type = 'credit' THEN amount ELSE 0 END) AS total_credit"),
        ])->first();

        $totalBalance = $balanceQuery->get()->groupBy(fn($transaction) =>
        trim(implode(' ', [
            $transaction->wallet->offTaker->first_name ?? '',
            $transaction->wallet->offTaker->middle_name ?? '',
            $transaction->wallet->offTaker->last_name ?? ''
        ]))
        )->map(fn($group) => $group->sortByDesc('created_at')->first()->wallet->balance)->sum();

        $paginatedResult = $walletTransactions->toArray();
        $paginatedResult['totals'] = [
            'debit' => round($totals->total_debit, 1),
            'credit' => round($totals->total_credit, 1),
            'balance' => round($totalBalance, 1),
        ];

        return response()->json($paginatedResult);


    }

    /**
     * Store a newly created WalletTransaction in storage.
     * POST /wallet-transactions
     */
    public function store(CreateWalletTransactionAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $walletTransaction = $this->walletTransactionRepository->create($input);

        return $this->sendResponse($walletTransaction->toArray(), 'Wallet Transaction saved successfully');
    }

    /**
     * Display the specified WalletTransaction.
     * GET|HEAD /wallet-transactions/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var WalletTransaction $walletTransaction */
        $walletTransaction = $this->walletTransactionRepository->find($id);

        if (empty($walletTransaction)) {
            return $this->sendError('Wallet Transaction not found');
        }

        return $this->sendResponse($walletTransaction->toArray(), 'Wallet Transaction retrieved successfully');
    }

    /**
     * Update the specified WalletTransaction in storage.
     * PUT/PATCH /wallet-transactions/{id}
     */
    public function update($id, UpdateWalletTransactionAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var WalletTransaction $walletTransaction */
        $walletTransaction = $this->walletTransactionRepository->find($id);

        if (empty($walletTransaction)) {
            return $this->sendError('Wallet Transaction not found');
        }

        $walletTransaction = $this->walletTransactionRepository->update($input, $id);

        return $this->sendResponse($walletTransaction->toArray(), 'WalletTransaction updated successfully');
    }

    /**
     * Remove the specified WalletTransaction from storage.
     * DELETE /wallet-transactions/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var WalletTransaction $walletTransaction */
        $walletTransaction = $this->walletTransactionRepository->find($id);

        if (empty($walletTransaction)) {
            return $this->sendError('Wallet Transaction not found');
        }

        $walletTransaction->delete();

        return $this->sendSuccess('Wallet Transaction deleted successfully');
    }
}
