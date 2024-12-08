<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateWalletAPIRequest;
use App\Http\Requests\API\UpdateWalletAPIRequest;
use App\Models\Wallet;
use App\Repositories\WalletRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class WalletAPIController
 */
class WalletAPIController extends AppBaseController
{
    private WalletRepository $walletRepository;

    public function __construct(WalletRepository $walletRepo)
    {
        $this->walletRepository = $walletRepo;
    }

    /**
     * Display a listing of the Wallets.
     * GET|HEAD /wallets
     */
    public function index(Request $request): JsonResponse
    {
        $wallets = $this->walletRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($wallets->toArray(), 'Wallets retrieved successfully');
    }

    /**
     * Store a newly created Wallet in storage.
     * POST /wallets
     */
    public function store(CreateWalletAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $wallet = $this->walletRepository->create($input);

        return $this->sendResponse($wallet->toArray(), 'Wallet saved successfully');
    }

    /**
     * Display the specified Wallet.
     * GET|HEAD /wallets/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Wallet $wallet */
        $wallet = $this->walletRepository->find($id);

        if (empty($wallet)) {
            return $this->sendError('Wallet not found');
        }

        return $this->sendResponse($wallet->toArray(), 'Wallet retrieved successfully');
    }

    /**
     * Update the specified Wallet in storage.
     * PUT/PATCH /wallets/{id}
     */
    public function update($id, UpdateWalletAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Wallet $wallet */
        $wallet = $this->walletRepository->find($id);

        if (empty($wallet)) {
            return $this->sendError('Wallet not found');
        }

        $wallet = $this->walletRepository->update($input, $id);

        return $this->sendResponse($wallet->toArray(), 'Wallet updated successfully');
    }

    /**
     * Remove the specified Wallet from storage.
     * DELETE /wallets/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Wallet $wallet */
        $wallet = $this->walletRepository->find($id);

        if (empty($wallet)) {
            return $this->sendError('Wallet not found');
        }

        $wallet->delete();

        return $this->sendSuccess('Wallet deleted successfully');
    }
}
