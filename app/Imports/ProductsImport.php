<?php

namespace App\Imports;
use App\Models\category;
use App\Models\Unit;
use App\Models\Product;
use App\Models\RateCard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\ToModel;
class ProductsImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    private $category_mapping;
    private $unit_mapping;
   

    public function __construct(
        Array $_category_mapping,
        Array $_unit_mapping,
        )
    {
        $this->category_mapping = $_category_mapping;
        $this->unit_mapping = $_unit_mapping;
       
    }
    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function model(array $row)
    {
     
        try {
            DB::beginTransaction();
            if (
                isset($row['item']) &&
                !is_null($row['item'])&&
                isset($row['description']) &&
                !is_null($row['description']) &&
                isset($row['category']) &&
                !is_null($row['category'])&&
                isset($row['packsize']) &&
                !is_null($row['packsize'])&&
                isset($row['unit_of_measure']) &&
                !is_null($row['unit_of_measure'])&&
                isset($row['cost']) &&
                !is_null($row['cost'])
            ) {
                
                $unit_id = null;
                if(isset($this->unit_mapping[strtolower($row['unit_of_measure'])]))
                $unit_id = $this->unit_mapping[strtolower($row['unit_of_measure'])];
                else{
                    $unit =Unit::updateOrCreate(
                        array(
                            'name' => $row['unit_of_measure'],
                        ));
                    $unit_id =$unit->id;
                }
                $category_id = null;
                if(isset($this->category_mapping[strtolower($row['category'])]))
                $category_id = $this->category_mapping[strtolower($row['category'])];
                else{
                    $category =category::updateOrCreate(
                        array(
                            'name' => $row['category'], 'description' => $row['category'],
                        ));
                    $category_id =$category->id;
                }
                $date=\Carbon\Carbon::now()->format('Y-m-d h:i:s');
                $product=Product::updateOrCreate(array(
                    'name' => $row['item'],
                ), array(
                    'name' => $row['item'],
                    'description' => $row['description'],
                    'category_id' => $category_id,
                    'unit_id' => $unit_id,
                    'pack_size' => $row['packsize'],
                    )
                );

                RateCard::updateOrCreate(array(
                    'item_id' => $product->id,
                    'item_type' => "product",
                    'amount' => $row['cost'],
                    'effective_date' => $date,
                ), array(
                    'name' => $row['item'],
                    'item_id' => $product->id,
                    'item_type' => "product",
                    'amount' => $row['cost'],
                    'effective_date' => $date,
                    )
                );

                DB::commit();
               
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::critical($exception);

        }
    }
}
