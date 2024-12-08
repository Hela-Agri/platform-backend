<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateProductAPIRequest;
use App\Http\Requests\API\UpdateProductAPIRequest;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\RateCard;
use App\Repositories\ProductRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
/**
 * Class ProductAPIController
 */
class ProductAPIController extends AppBaseController
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepo)
    {
        $this->productRepository = $productRepo;
    }

    /**
     * Display a listing of the Products.
     * GET|HEAD /products
     */
    public function index(Request $request): JsonResponse
    {
        $products = $this->productRepository->with(['unit','category'])
        
        ->paginate($request->get('limit', 50));

        return $this->sendResponse($products->toArray(), 'Products retrieved successfully');
    }

    /**
     * Store a newly created Product in storage.
     * POST /products
     */
    public function store(CreateProductAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $product = $this->productRepository->create($input);

        return $this->sendResponse($product->toArray(), 'Product saved successfully');
    }

    /**
     * Display the specified Product.
     * GET|HEAD /products/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Product $product */
        $product = $this->productRepository->find($id);

        if (empty($product)) {
            return $this->sendError('Product not found');
        }

        return $this->sendResponse($product->toArray(), 'Product retrieved successfully');
    }

    /**
     * Update the specified Product in storage.
     * PUT/PATCH /products/{id}
     */
    public function update($id, UpdateProductAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Product $product */
        $product = $this->productRepository->find($id);

        if (empty($product)) {
            return $this->sendError('Product not found');
        }

        $product = $this->productRepository->update($input, $id);

        if($product){//update the rate card name
           
            RateCard::where('item_id', $product->id)->where('item_type', 'product')
            ->update([
                'name' =>  $product->name
                ]);
 
        }

        return $this->sendResponse($product->toArray(), 'Product updated successfully');
    }

    /**
     * Remove the specified Product from storage.
     * DELETE /products/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Product $product */
        $product = $this->productRepository->find($id);

        if (empty($product)) {
            return $this->sendError('Product not found');
        }

        $product->delete();

        return $this->sendSuccess('Product deleted successfully');
    }

    /**
     * Store a newly created DoctorRate in storage.
     * POST /doctor-rate-template
     */
    public function importProducts(Request $request): JsonResponse
    {
        try {
            $file = $request->file('product_list');
            
            if ($file){
                $category_mapping =array();
                $unit_mapping =array();
                $categories=Category::get();
                $units=Unit::get();

                foreach($categories as $category){
                    $category_mapping[strtolower($category->name)]=$category->id;
                }
                foreach($units as $unit){
                    $unit_mapping[strtolower($unit->name)]=$unit->id;
                }
               
                Excel::import(new ProductsImport(
                    $category_mapping ,
                    $unit_mapping
                ), $request->file('product_list'));
            }
       
            return $this->sendSuccess('Products imported successfully');
        } catch (\Exception $exception) {
            \Log::critical($exception);
            return $this->sendError($exception);
        }
    }
}
