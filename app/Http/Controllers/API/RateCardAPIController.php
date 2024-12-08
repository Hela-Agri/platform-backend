<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateRateCardAPIRequest;
use App\Http\Requests\API\UpdateRateCardAPIRequest;
use App\Models\Product;
use App\Models\RateCard;
use App\Models\Service;
use App\Repositories\RateCardRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Log;

/**
 * Class RateCardAPIController
 */
class RateCardAPIController extends AppBaseController
{
    private RateCardRepository $rateCardRepository;

    public function __construct(RateCardRepository $rateCardRepo)
    {
        $this->rateCardRepository = $rateCardRepo;
    }

    /**
     * Display a listing of the RateCards.
     * GET|HEAD /rate-cards
     */
    public function index(Request $request): JsonResponse
    {
        $rateCards = $this->rateCardRepository
        ->select('rate_cards.*')
        ->when($request->has('type'),function($q) use($request){
            return $q->where('rate_cards.item_type',$request->get('type'));
        })
        ->leftJoin("rate_cards as rc1", function ($join) {
            $join->on('rate_cards.item_id', '=', 'rc1.item_id');
            $join->on('rate_cards.effective_date', '<', 'rc1.effective_date');
        })
        ->whereNull('rc1.item_id')
        ->orderBy('name','asc')
        ->paginate($request->get('limit', 50));

        return $this->sendResponse($rateCards->toArray(), 'Rate Cards retrieved successfully');
    }

    /**
     * Store a newly created RateCard in storage.
     * POST /rate-cards
     */
    public function store(CreateRateCardAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        foreach (json_decode($input['records']) as $record) {
            $item_type = null;
            $name = '';
            if ($input['item_type'] == 'Product') {
                $item_type = 'product';
                $name = Product::find($record->item_id)->name;
            } elseif ($input['item_type'] == 'Service') {
                $item_type = 'service';
                $name = Service::find($record->item_id)->name;
            }
            $this->rateCardRepository->create([
                'name' => $name,
                'item_id' => $record->item_id,
                'item_type' => $item_type,
                'amount' => $record->amount,
                'effective_date' => $record->effective_date,
            ]);
        }

        return $this->sendSuccess('Rate Card saved successfully');
    }

    /**
     * Display the specified RateCard.
     * GET|HEAD /rate-cards/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var RateCard $rateCard */
        $rateCard = $this->rateCardRepository->find($id);

        if (empty($rateCard)) {
            return $this->sendError('Rate Card not found');
        }

        return $this->sendResponse($rateCard->toArray(), 'Rate Card retrieved successfully');
    }

    /**
     * Update the specified RateCard in storage.
     * PUT/PATCH /rate-cards/{id}
     */
    public function update($id, UpdateRateCardAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var RateCard $rateCard */
        $rateCard = $this->rateCardRepository->find($id);

        if (empty($rateCard)) {
            return $this->sendError('Rate Card not found');
        }

        $rateCard = $this->rateCardRepository->update($input, $id);

        return $this->sendResponse($rateCard->toArray(), 'RateCard updated successfully');
    }

    /**
     * Remove the specified RateCard from storage.
     * DELETE /rate-cards/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var RateCard $rateCard */
        $rateCard = $this->rateCardRepository->find($id);

        if (empty($rateCard)) {
            return $this->sendError('Rate Card not found');
        }

        $rateCard->delete();

        return $this->sendSuccess('Rate Card deleted successfully');
    }
}
