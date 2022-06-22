<?php
namespace App\Exports;

use App\Models\ProductVariant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use DB;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
class StockReportExport implements FromCollection, WithMapping, WithHeadings, WithColumnFormatting, WithStyles, WithColumnWidths
{
    public function collection()
    {   
        
        $query = ProductVariant::join('product', 'product.id', '=', 'product_variant.product_id');
        $query->select('product_variant.id', 'product_variant.product_id', 'product.name', 'product_variant.variant', 'product_variant.purchase_price', 'product_variant.sell_price');
        $query->withCount(['stock'=> function($q){
            $q->select(DB::raw("SUM(stock)"));
        },
        'sale'
        ]);

        $query->when(!empty($search), function($query, $search){
                $query->where('name', 'LIKE', '%' . $search . '%');
        });
        
        $res = $query->get();

        $sorted = $res->sortBy('name');

        return $sorted->values();
    }

    
    /**
    * @var Product Variant $invoice
    */
    public function map($product): array
    {
        return [
            $product->name,
            $product->purchase_price,
            $product->sell_price,
            $product->stock_count,
            $product->stock_count * $product->purchase_price,
            $product->stock_count * $product->sell_price,
            ($product->sale_count) ? $product->sale_count : 0,
        ];
    }

    public function headings(): array
    {
        return [
            'Product',
            'Purchase Price',
            'Sell Price',
            'Current Stock',
            'Current Stock Value (by purchase price)',
            'Current Stock Value (by sell price)',
            'Total Sold'
        ];
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 42,
            'B' => 16,   
            'C' => 16,
            'D' => 13, 
            'E' => 19, 
            'F' => 13,         
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => '_-Rp* #.##0_-;-Rp* #.##0_-;_-Rp* "-"_-;_-@_-',
            'C' => '_-Rp* #.##0_-;-Rp* #.##0_-;_-Rp* "-"_-;_-@_-',
            'E' => '_-Rp* #.##0_-;-Rp* #.##0_-;_-Rp* "-"_-;_-@_-',
            'F' => '_-Rp* #.##0_-;-Rp* #.##0_-;_-Rp* "-"_-;_-@_-',
        ];
    }

    
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(1)->getFont()->setBold(true);
        $sheet->getStyle('A')->getFont()->setBold(true);
    }
}