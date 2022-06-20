<?php

namespace App\Http\Controllers\Backend;

use App\Models\Product;
use App\Models\ProductVariant;


use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Helpers\ReportHelp;
use Carbon\Carbon;

use App\Helpers\Collection;
use App\Exports\StockReportExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }
    
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function getProfitLoss(Request $request)
    {

        $start_date = Carbon::parse($request->from)->subDay()->format('Y-m-d');
        $end_date = Carbon::parse($request->to)->format('Y-m-d');

        $data = Collect([]);

        // Stock 
        $data->put('opening_stock', ReportHelp::getOpeningClosingStock($start_date, true, false));
        $data->put('opening_stock_by_sp', ReportHelp::getOpeningClosingStock($start_date, true, true));

        
        $data->put('closing_stock', ReportHelp::getOpeningClosingStock($end_date, false, false));
        $data->put('closing_stock_by_sp', ReportHelp::getOpeningClosingStock($end_date, false, true));

        //Get Purchase details
        $purchase_details = ReportHelp::getPurchaseTotal(
            $start_date,
            $end_date,
        );

        // Purchase
        $data->put('total_purchase', !empty($purchase_details['total_purchase_exc_tax']) ? $purchase_details['total_purchase_exc_tax'] : 0);
        $data->put('total_purchase_discount', !empty($purchase_details['total_purchase_discount']) ? $purchase_details['total_purchase_discount'] : 0);
        $data->put('total_purchase_return', !empty($purchase_details['total_return']) ? $purchase_details['total_return'] : 0);


        //Get Sell details
        $sell_details = ReportHelp::getSellTotal(
            $start_date,
            $end_date,
        );
        
        // Sales
        $data->put('total_sale', !empty($sell_details['total_sell_exc_tax']) ? $sell_details['total_sell_exc_tax'] : 0);
        $data->put('total_sale_discount', !empty($sell_details['total_purchase_exc_tax']) ? $sell_details['total_purchase_exc_tax'] : 0);
        $data->put('total_sale_return', !empty($sell_details['total_sale_return']) ? $sell_details['total_sale_return'] : 0);
        $data->put('total_sale_shipping', !empty($sell_details['total_shipping_charges']) ? $sell_details['total_shipping_charges'] : 0);
        // dd();

        // Gross Profit
        
        $gross_profit = ReportHelp::getGrossProfit(
            $start_date,
            $end_date,
        );

        $data->put('gross_profit', $gross_profit);

        
        $net_profit = $gross_profit + $data['total_sale_shipping'];
        
        $data->put('net_profit', $net_profit);

        return Inertia::render('Backend/Report/ProfitLoss',[
            'data' => $data->all(),
        ]);
    }

    public function getStockReport(Request $request){

        $search = $request->search;
        $sort = ($request->sort) ? $request->sort : 'name';
        $sort_dir = ($request->sortDir) ? $request->sortDir : 'asc';


        // $res = Product::select('id', 'name', 'show_web', 'slug', 'category_id', 'brand_id')
        // ->withCount([
        //     'stock' => function ($query) {
        //         $query->select(DB::raw("SUM(stock)"));
        //     },
        //     'sale'
        // ])
        // ->with([
        //     'variant'
        // ])
        // 
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

        if($sort_dir == 'asc'){
            $sorted = $res->sortBy($sort);
        }else{
            $sorted = $res->sortByDesc($sort);
        }

        if($request->excel){
            return Excel::download(new StockReportExport, 'contoh.xlsx');   
        }

        $data = (new Collection($sorted->values()))->paginate(20);
        
        return Inertia::render('Backend/Report/StockReport',[
            'dataList' => $data,
        ]);
    }


}