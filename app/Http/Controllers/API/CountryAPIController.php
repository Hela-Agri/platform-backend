<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCountryAPIRequest;
use App\Http\Requests\API\UpdateCountryAPIRequest;
use App\Models\Country;
use App\Repositories\CountryRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class CountryAPIController
 */
class CountryAPIController extends AppBaseController
{
    private CountryRepository $countryRepository;

    public function __construct(CountryRepository $countryRepo)
    {
        $this->countryRepository = $countryRepo;
    }

    /**
     * Display a listing of the Countries.
     * GET|HEAD /countries
     */
    public function index(Request $request): JsonResponse
    {
        $countries = $this->countryRepository->paginate($request->get('limit', 200));

        return $this->sendResponse($countries->toArray(), 'Countries retrieved successfully');
    }

    /**
     * Store a newly created Country in storage.
     * POST /countries
     */
    public function store(CreateCountryAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $country = $this->countryRepository->create($input);

        return $this->sendResponse($country->toArray(), 'Country saved successfully');
    }

    /**
     * Display the specified Country.
     * GET|HEAD /countries/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Country $country */
        $country = $this->countryRepository->find($id);

        if (empty($country)) {
            return $this->sendError('Country not found');
        }

        return $this->sendResponse($country->toArray(), 'Country retrieved successfully');
    }

    /**
     * Update the specified Country in storage.
     * PUT/PATCH /countries/{id}
     */
    public function update($id, UpdateCountryAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Country $country */
        $country = $this->countryRepository->find($id);

        if (empty($country)) {
            return $this->sendError('Country not found');
        }

        $country = $this->countryRepository->update($input, $id);

        return $this->sendResponse($country->toArray(), 'Country updated successfully');
    }

    /**
     * Remove the specified Country from storage.
     * DELETE /countries/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Country $country */
        $country = $this->countryRepository->find($id);

        if (empty($country)) {
            return $this->sendError('Country not found');
        }

        $country->delete();

        return $this->sendSuccess('Country deleted successfully');
    }
}
