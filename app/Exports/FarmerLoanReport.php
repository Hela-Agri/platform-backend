<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FarmerLoanReport implements ShouldAutoSize,WithDrawings, WithHeadings, FromArray, WithStyles, WithTitle, WithDefaultStyles
{

    use Exportable;

    protected $records;
    protected $title;
    protected $letter_head;
    protected $logo;

    public function __construct
    (
        Collection $_records,
        $logo,
        $letter_head,
        $title
    )
    {
        $this->records = $_records;
        $this->logo = $logo;
        $this->letter_head = $letter_head;
        $this->title = $title;
    }

    public function array(): array
    {
        $rows = [];
        $grandTotal = 0;
        $rows[] = ['', '', '', '', ''];
        $rows[] = ['', '', '', '', ''];
        $rows[] = ['', '', '', '', ''];
        foreach ($this->records as $date => $record) {
            $rows[] = [$date, '', '', '', ''];

            $dailyTotal = 0;

            foreach ($record->items as $item) {
                $amount = floatval(str_replace(',', '', $item['amount']));
                $dailyTotal += $amount;

                $rows[] = [
                    $item['product_name'] ?? '',
                    $item['category_name'] ?? '',
                    number_format(floatval(str_replace(',', '', $item['quantity']))),
                    number_format(floatval(str_replace(',', '', $item['rate']))),
                    number_format($amount),
                ];
            }

            $rows[] = ['', '', '', '', ''];

            $grandTotal += $dailyTotal;
        }

        $rows[] = [
            'Total Loaned Inputs', '', '', '',
            number_format($grandTotal),
        ];

        return $rows;
    }


    public function headings(): array
    {
        return [
            [''],
            ['Product Name', 'Category', 'Quantity', 'Rate (KES)', 'Total (KES)']
        ];
    }

    public function defaultStyles(Style $defaultStyle): array
    {
        return [
            'font' => [
                'size' => 10,
                'name' => 'Aptos Narrow',
            ],
        ];
    }

    public function drawings(): Drawing
    {

     $drawing = new Drawing();



     if(isset($this->logo)){

         //check if file exist and only make a copy if it does not
         $temp_path = public_path('logo/'.$this->logo->file_name);

         if (!file_exists($temp_path)) {

             if(!\File::isDirectory(public_path('logo/'))){
                 \File::makeDirectory(public_path('logo/'), 0777, true, true);
             }
             file_put_contents($temp_path, file_get_contents($this->logo->path));
         }
         $drawing->setName($this->logo->file_name);
         $drawing->setDescription($this->logo->file_name);
         $drawing->setPath($temp_path);
         $drawing->setHeight(100);
         $drawing->setCoordinates('A1');
     }
     return $drawing;


    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $address= str_replace('&nbsp;',' ',strip_tags($this->letter_head));
        $sheet->getRowDimension('1')->setRowHeight(80);
        $sheet->mergeCells('A1:B1');
        $sheet->mergeCells('C1:E1');
        $sheet->setCellValue('C1',$address);
        $sheet->getStyle('C1')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A1')->getAlignment()->setVertical('top');
        $sheet->getStyle('C1')->getAlignment()->setVertical('top');
        $sheet->getStyle('C1')->getFont()->setSize(10);
        return [
            // Style the first row .
            2 => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '9ec14c'],
                ],
                'font' => [
                    'size' => 10,
                    'name' => 'Aptos Narrow',
                    'color' => ['argb' => 'FFFFFF']
                ],
            ],

        ];
    }

    public function title(): string
    {
        return $this->title;
    }
}