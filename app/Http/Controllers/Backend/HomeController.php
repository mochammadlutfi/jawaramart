<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Sale;
use App\Models\Purchase;
use Carbon\CarbonPeriod;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    { 
        $startDate = !empty($request->startDate) ? Carbon::parse($request->startDate)->format('Y-m-d') : Carbon::now()->startofmonth()->format('Y-m-d');
        $endDate = !empty($request->endDate) ? Carbon::parse($request->endDate)->format('Y-m-d') : Carbon::now()->format('Y-m-d');

        $today = Carbon::now()->format('m');
        $customer = User::where(function ($query) use ($startDate, $endDate) {
            $query->whereDate("created_at", ">=", $startDate)
                ->orWhereDate("created_at", "=", $endDate);
        })->get()->count();
        $sale_orders = Sale::where(function ($query) use ($startDate, $endDate) {
            $query->whereDate("date", ">=", $startDate)
                ->orWhereDate("date", "=", $endDate);
        })->get()->count();
        $purchase_orders = Purchase::where(function ($query) use ($startDate, $endDate) {
            $query->whereDate("date", ">=", $startDate)
                ->orWhereDate("date", "=", $endDate);
        })->get()->count();
        $sale_profit = Sale::where(function ($query) use ($startDate, $endDate) {
            $query->whereDate("date", ">=", $startDate)
                ->orWhereDate("date", "=", $endDate);
        })->get()->sum('grand_total');
        
        $overview = collect([
            [
                'title' => 'New Customers',
                'data' => $customer,
                'type' => 'count',
            ],
            [
                'title' => 'Sale Orders',
                'data' => $sale_orders,
                'type' => 'count',
            ],
            [
                'title' => 'Purchase Orders',
                'data' => $purchase_orders,
                'type' => 'count',
            ],
            [
                'title' => 'Revenue',
                'data' => $sale_profit,
                'type' => 'money',
            ],
        ]);

        return Inertia::render('Backend/Dashboard',[
            'overview' => $overview,
            'chartSales' => $this->chartSale(),
        ]);
    }

    private function chartSale(){
        $today = Carbon::now();
        $start = Carbon::now()->subDays(7);
        // dd($start->diffInDays($today));
        $i = 0;
        $label = array();
        $sale = array();
        $sukarela = array();
        $period = CarbonPeriod::create(Carbon::now()->subDay(6), Carbon::now());
        

        foreach($period as $p){
            $label[] = $p->translatedFormat('l, d M Y');
            $saleEloq = Sale::whereDate('date', $p);
            $sale[] = $saleEloq->sum('grand_total');
        }

        // while ($i <= $start->diffInDays($today+1)) {

        //     $dayOfWeek = Carbon::now()->subDays(7)->addDays($i);
        //     $label[] = $dayOfWeek->translatedFormat('l, d M Y');

        //     $saleEloq = Sale::whereDate('date', Carbon::now()->subDays(7)->addDays($i));

        //     // ->where('status', 'done');

        //     $sale[] = $saleEloq->sum('grand_total');
        //     $i++;
        // }
        $response = Collect([
            "label" => $label,
            "sale" => $sale,
        ]);

        return $response;
    }
}
