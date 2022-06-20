<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use DB;

use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\SaleLine;
use App\Models\Purchase;
use App\Models\PurchaseLine;
use App\Models\PurchaseReturn;
use App\Models\Payment;
class ReportHelp
{
    /**
     * Calculates total stock on the given date
     *
     * @param string $date
     * @param int $location_id
     * @param boolean $is_opening = false
     *
     * @return float
     */
    public function getOpeningClosingStock($date, $is_opening = false, $by_sale_price = false, $filters = [])
    {
        $query = PurchaseLine::join(
            'purchases as purchase',
            'purchase_lines.purchase_id',
            '=',
            'purchase.id'
        );

        $price_query_part = "(purchase_lines.unit_price + COALESCE(purchase_lines.tax_amount, 0))";

        if ($by_sale_price) {
            $price_query_part = 'v.sell_price';
        } 

        $query->leftjoin('product_variant as v', 'v.id', '=', 'purchase_lines.variant_id')
                ->leftjoin('product as p', 'p.id', '=', 'purchase_lines.product_id');

        if (!empty($filters['category_id'])) {
            $query->where('p.category_id',$filters['category_id']);
        }
        if (!empty($filters['sub_category_id'])) {
            $query->where('p.sub_category_id',$filters['sub_category_id']);
        }
        if (!empty($filters['brand_id'])) {
            $query->where('p.brand_id',$filters['brand_id']);
        }
        if (!empty($filters['unit_id'])) {
            $query->where('p.unit_id',$filters['unit_id']);
        }
        if (!empty($filters['user_id'])) {
            $query->where('purchase.created_by',$filters['user_id']);
        }

        //If opening
        if ($is_opening) {
            $next_day = Carbon::parse($date)->addDay()->format('Y-m-d');
            $query->where(function ($query) use ($date, $next_day) {
                $query->whereDate("purchase.date", ">=", $date)
                    ->orWhereDate("purchase.date", "=", $next_day)
                    ->where('is_opening', 1);
            });
        } else {
            $query->whereDate("purchase.date", "<=", $date);
        }
                    
        $query->select(
            DB::raw("SUM((purchase_lines.qty - purchase_lines.qty_returned) * $price_query_part
                        ) as stock")
        );

        //Check for permitted locations of a user

        $details = $query->first();
        // dd($details->toArray());
        return (int)$details->stock;
    }

        /**
     * Gives the total purchase amount for a business within the date range passed
     *
     * @param int $business_id
     * @param int $transaction_id
     *
     * @return array
     */
    public function getPurchaseTotal($start_date = null, $end_date = null)
    {
        $query = Purchase::select('grand_total', 'discount_amount',
                            DB::raw("(total - tax_amount) as total_exc_tax"),
                            DB::raw("SUM((SELECT SUM(tp.amount) FROM payment as tp WHERE tp.paymenttable_type='App\Models\Purchase' AND tp.paymenttable_id=purchases.id)) as total_paid"),
                            DB::raw("SUM((SELECT SUM(rt.grand_total) FROM purchase_return as rt WHERE rt.purchase_id=purchases.id)) as total_return"),
                        )
                        ->where('is_opening', 0)
                        ->groupBy('purchases.id');

        if (!empty($start_date) && !empty($end_date)) {
            $query->whereDate('purchases.date', '>=', $start_date)
                ->whereDate('purchases.date', '<=', $end_date);
        }

        if (empty($start_date) && !empty($end_date)) {
            $query->whereDate('purchases.date', '<=', $end_date);
        }

        $purchase_details = $query->get();

        $output['total_purchase_inc_tax'] = $purchase_details->sum('grand_total');
        $output['total_purchase_exc_tax'] = $purchase_details->sum('total_exc_tax');
        $output['purchase_due'] = $purchase_details->sum('grand_total') - $purchase_details->sum('total_paid');
        $output['total_return'] = $purchase_details->sum('total_return');
        $output['total_purchase_discount'] = $purchase_details->sum('discount_amount');

        return $output;
    }

    
    /**
     * Gives the total sell amount for a business within the date range passed
     *
     * @param int $business_id
     * @param int $transaction_id
     *
     * @return array
     */
    public function getSellTotal($start_date = null, $end_date = null)
    {
        $query = Sale::where('status', 'done')
                    ->select(
                        'id',
                        'grand_total',
                        DB::raw("(grand_total - tax_amount) as total_exc_tax"),
                        DB::raw("SUM((SELECT SUM(tp.amount) FROM payment as tp WHERE tp.paymenttable_type='App\Models\Sale' AND tp.paymenttable_id=sales.id)) as total_paid"),
                        DB::raw('SUM(total) as total_before_tax'),
                        DB::raw("SUM((SELECT SUM(rt.grand_total) FROM sale_return as rt WHERE rt.sale_id=sales.id)) as total_return"),
                        'shipping_cost'
                    )
                    ->groupBy('sales.id');

        //Check for permitted locations of a user
        if (!empty($start_date) && !empty($end_date)) {
            $query->whereDate('sales.date', '>=', $start_date)
                ->whereDate('sales.date', '<=', $end_date);
        }

        if (empty($start_date) && !empty($end_date)) {
            $query->whereDate('sales.date', '<=', $end_date);
        }

        if (!empty($created_by)) {
            $query->where('sales.created_by', $created_by);
        }

        $sell_details = $query->get();

        $output['total_sell_inc_tax'] = $sell_details->sum('grand_total');
        $output['total_sale_return'] = $sell_details->sum('total_return');
        $output['total_sell_exc_tax'] = $sell_details->sum('total_exc_tax');
        $output['invoice_due'] = $sell_details->sum('grand_total') - $sell_details->sum('total_paid');
        $output['total_shipping_charges'] = $sell_details->sum('shipping_cost');

        return $output;
    }

    
    public function getGrossProfit($start_date = null, $end_date = null, $user_id = null)
    {
        $query = SaleLine::leftjoin('sales', 'sales.id', '=', 'sale_lines.sale_id')
        ->join('product_variant as v', 'sale_lines.variant_id', '=', 'v.id')
        ->leftjoin('purchase_lines as pl', 'sale_lines.variant_id', '=', 'pl.variant_id')
        ->leftjoin('sale_return_lines as sr', 'sale_lines.variant_id', '=', 'sr.variant_id')
        // ->leftjoin(DB::Raw('select variant_id, round(AVG(unit_price),0) as p_price FROM purchase_lines group by variant_id as PL'), 'PL.variant_id', '=', 'sale_lines.variant_id')
        ->where('sales.status', 'done');
        
        // ->groupBy('sale_lines.variant_id');

        if (!empty($start_date) && !empty($end_date) && $start_date != $end_date) {
            $query->whereDate('sales.date', '>=', $start_date)
                ->whereDate('sales.date', '<=', $end_date);
        }
        if (!empty($start_date) && !empty($end_date) && $start_date == $end_date) {
            $query->whereDate('sales.date', $end_date);
        }

        //Filter by the location
        if (!empty($user_id)) {
            $query->where('sales.staff_id', $user_id);
        }
        
        $sale = $query->select(
            // DB::raw('IFNULL(sr.qty, 0) as qty_return'),
            DB::raw('SUM( (sale_lines.qty - IFNULL(sr.qty, 0)) * (sale_lines.unit_price - IFNULL(pl.net_price, v.purchase_price)) ) as gross_profit'),
            // 'sale_lines.qty', 'sale_lines.unit_price', 'v.purchase_price',
            // DB::raw('(sale_lines.unit_price - v.purchase_price) as hpp'),
        )->first();
        // dd($sale->toArray());
        $gross_profit = !empty($sale->gross_profit) ? $sale->gross_profit : 0;

        return $gross_profit;
    }

}