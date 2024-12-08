<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\PaymentModeRequest;
use App\Models\PaymentMode;
use App\Repositories\PaymentModeRepository;
use Illuminate\Http\Request;
use Response;

/**
 * Class PaymentModeController
 * @package App\Http\Controllers\API
 */

class PaymentModeController extends AppBaseController
{
    /** @var  PaymentModeRepository */
    private $paymentModeRepository;

    public function __construct(PaymentModeRepository $paymentModeRepo)
    {
        $this->paymentModeRepository = $paymentModeRepo;
    }

    /**
     * Display a listing of the PaymentMode.
     * GET|HEAD /paymentModes
     *
     * @param Request $request
     * @return Response
     */
    public function index()
    {
        $paymentModes = $this->paymentModeRepository->paginate(10);

        return $this->sendResponse($paymentModes, 'Payment Modes retrieved successfully');
    }

    /**
     * Store a newly created PaymentMode in storage.
     * POST /paymentModes
     *
     * @param PaymentModeRequest $request
     *
     * @return Response
     */
    public function store(PaymentModeRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response([
                'status' => 'failed',
                'errors' => $request->validator->errors()
            ], 422);
        }

        $input = $request->all();

        $paymentMode = $this->paymentModeRepository->create($input);

        return $this->sendResponse($paymentMode->toArray(), 'PaymentMode saved successfully');
    }

    /**
     * Display the specified PaymentMode.
     * GET|HEAD /paymentModes/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var PaymentMode $paymentMode */
        $paymentMode = $this->paymentModeRepository->find($id);

        if (empty($paymentMode)) {
            return $this->sendError('PaymentMode not found');
        }

        return $this->sendResponse($paymentMode->toArray(), 'PaymentMode retrieved successfully');
    }

    /**
     * Update the specified PaymentMode in storage.
     * PUT/PATCH /paymentModes/{id}
     *
     * @param int $id
     * @param PaymentModeRequest $request
     *
     * @return Response
     */
    public function update($id, PaymentModeRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response([
                'status' => 'failed',
                'errors' => $request->validator->errors()
            ], 422);
        }

        $input = $request->all();

        /** @var PaymentMode $paymentMode */
        $paymentMode = $this->paymentModeRepository->find($id);

        if (empty($paymentMode)) {
            return $this->sendError('PaymentMode not found');
        }

        $paymentMode = $this->paymentModeRepository->update($input, $id);

        return $this->sendResponse($paymentMode->toArray(), 'PaymentMode updated successfully');
    }

    /**
     * Remove the specified PaymentMode from storage.
     * DELETE /paymentModes/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var PaymentMode $paymentMode */
        $paymentMode = $this->paymentModeRepository->find($id);

        if (empty($paymentMode)) {
            return $this->sendError('PaymentMode not found');
        }

        $paymentMode->delete();

        return $this->sendSuccess('PaymentMode deleted successfully');
    }
}
