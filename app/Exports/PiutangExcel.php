<?php

namespace App\Exports;

use App\Models\Sale;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use DB;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
class PiutangExcel implements FromCollection, WithMapping, WithHeadings, WithColumnFormatting, WithColumnWidths
{

    // protected $year;
    
    // public function __construct(int $year)
    // {
    //     $this->year = $year;
    // }

    public function headings(): array
    {
        return [
            'Customer',
            'NIP',
            'Amount Due',
            'Last Transaction',
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {   
        $today = Carbon::now()->format('m');
        
        $data = Sale::select(DB::raw('SUM(grand_total) as grand_total, customer_id, status, payment_status, max(date) as date'))
        ->with([
            'payment' => function($q){
                $q->where('payment_method_id', 2)
                ->where('validated_at', Null);
            },
            'customer:id,name,avatar,nip'
        ])
        ->groupBy('customer_id')
        ->where('payment_status', 'pending')
        ->get();


        return $data;
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 26,
            'B' => 16,   
            'C' => 16,
            'D' => 19,         
        ];
    }

    
    /**
    * @var Invoice $invoice
    */
    public function map($data): array
    {
        return [
            $data->customer->name,
            $data->customer->nip,
            $data->grand_total,
            $data->date,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER,
        ];
    }
}
