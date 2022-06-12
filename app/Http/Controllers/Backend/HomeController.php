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

        $today = Carbon::now()->format('m');
        // dd($period->toArray());
        $customer = User::whereMonth('created_at', $today)->get()->count();
        $sale_orders = Sale::whereMonth('date', $today)->get()->count();
        $purchase_orders = Purchase::whereMonth('date', $today)->get()->count();
        $sale_profit = Sale::whereMonth('date', $today)->get()->sum('grand_total');

        
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
                'title' => 'Earnings',
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
