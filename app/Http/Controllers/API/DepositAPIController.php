<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDepositAPIRequest;
use App\Http\Requests\API\UpdateDepositAPIRequest;
use App\Models\Deposit;
use App\Models\Status;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Repositories\DepositRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Log;

/**
 * Class DepositAPIController
 */
class DepositAPIController extends AppBaseController
{
    private DepositRepository $depositRepository;

    public function __construct(DepositRepository $depositRepo)
    {
        $this->depositRepository = $depositRepo;
    }

    /**
     * Display a listing of the Deposits.
     * GET|HEAD /deposits
     * @throws \Exception
     */
    public function index(Request $request): JsonResponse
    {
        $deposits = $this->depositRepository
            ->with(['user', 'status'])
            ->orderBy('created_at', 'DESC')
            ->paginate($request->get('limit', 50));

        return $this->sendResponse($deposits->toArray(), 'Deposits retrieved successfully');
    }

    /**
     * Store a newly created Deposit in storage.
     * POST /deposits
     */
    public function store(CreateDepositAPIRequest $request): JsonResponse
    {
        $input = $request->all();


            $input['status_id'] = Status::where('code', 'PENDING')->first()->id;

            $deposit = $this->depositRepository->create($input);

            return $this->sendResponse($deposit->toArray(), 'Deposit saved successfully');

    }

    /**
     * Display the specified Deposit.
     * GET|HEAD /deposits/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Deposit $deposit */
        $deposit = $this->depositRepository->find($id);

        if (empty($deposit)) {
            return $this->sendError('Deposit not found');
        }

        return $this->sendResponse($deposit->toArray(), 'Deposit retrieved successfully');
    }

    /**
     * Update the specified Deposit in storage.
     * PUT/PATCH /deposits/{id}
     */
    public function update($id, UpdateDepositAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Deposit $deposit */
        $deposit = $this->depositRepository->find($id);

        if (empty($deposit)) {
            return $this->sendError('Deposit not found');
        }

        Log::critical($deposit->user_id);

        $input['balance'] = $input['requested_amount'] - $input['allowed_amount'];
        $input['status_id'] = Status::where('code', 'APPROVED')->first()->id;

        $wallet = Wallet::where('user_id', $deposit->user_id)->firstOrFail();

        if (!empty($wallet)) {
            $wallet_trans = WalletTransaction::create([
                'amount' => $input['allowed_amount'],
                'wallet_id' => $wallet->id,
                'type' => 'credit',
                'user_id' => $deposit->user_id
            ]);

            $input['wallet_transaction_id'] = $wallet_trans->id;
        } else {
            return $this->sendError('Wallet not found');
        }

        $deposit = $this->depositRepository->update($input, $id);

        $wallet_balance = $wallet->balance + $input['allowed_amount'];

        $wallet->update([
            'balance' => $wallet_balance
        ]);

        return $this->sendResponse($deposit->toArray(), 'Deposit updated successfully');
    }

    /**
     * Remove the specified Deposit from storage.
     * DELETE /deposits/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Deposit $deposit */
        $deposit = $this->depositRepository->find($id);

        if (empty($deposit)) {
            return $this->sendError('Deposit not found');
        }

        $deposit->delete();

        return $this->sendSuccess('Deposit deleted successfully');
    }
}
