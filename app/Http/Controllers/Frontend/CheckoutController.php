<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Session;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use App\Models\Cart;
use App\Models\UserAddress;
use App\Models\Sale;
use App\Models\SaleLine;
use App\Models\Payment;
use App\Helpers\GeneralHelp;

use Carbon\Carbon;
class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        if ($request->isMethod('post')) {
            Session::put('cart', $request->cart);
            $cart = $request->cart;
        }else if($request->isMethod('get')){
            if(Session::has('cart') && count(Session::get('cart')) > 0){
               $cart = Session::get('cart');
            }else{
                return redirect()->route('cart.index');
            }
        }

        $user_id = auth()->guard('web')->user()->id;
        $address = UserAddress::where('user_id', $user_id)->where('is_primary', 1)->first();

        return Inertia::render('Frontend/Checkout/Shipping',[
            'cart' => $cart,
            'address' => $address,
        ]);

    }

    public function payment(Request $request)
    {
        // dd($request->all());
        if ($request->isMethod('post')) {
            Session::put('checkout', $request->all());
            $data = $request->all();
        }else if($request->isMethod('get')){
            if(Session::has('checkout') && count(Session::get('checkout')) > 0){
                $data = Session::get('checkout');
            }else{
                return redirect()->route('cart.index');
            }
        }
        
        return Inertia::render('Frontend/Checkout/Payment',[
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        // dd($code);
        DB::beginTransaction();
        try{

            $code = GeneralHelp::generate_payment_code($request->payment_method);

            $checkout = Session::get('checkout');
            $user_id = auth()->guard('web')->user()->id;
            $cart = $request->session()->get('cart');

            $data = new Sale();
            $data->is_web = 1;
            $data->date = Carbon::now();
            $data->ref = GeneralHelp::generate_ref_sale();
            $data->customer_id = $user_id;
            $data->delivery_id = $checkout['delivery_id'];
            $data->total = $checkout['sub_total'];
            $data->shipping_cost = $checkout['shipping']['price'];
            $data->grand_total = $checkout['sub_total'] + $checkout['shipping']['price'] + $code;
            $data->status = 'pending';
            $data->payment_status = 'unpaid';
            $data->payment_due = Carbon::now()->addDay(1);
            $data->save();

            foreach($checkout['products'] as $i){
                $line = new SaleLine();
                $line->product_id = $i['variant_id'];
                $line->variant_id = $i['id'];
                $line->unit_price = $i['unit_price'];
                $line->net_price = $i['unit_price'];
                $line->qty = $i['qty'];
                $line->sub_total = $i['unit_price'] * $i['qty'];
                $data->line()->save($line);
            }
            
            $payment = new Payment();
            $payment->amount = $checkout['sub_total'] + $checkout['shipping']['price'];
            $payment->amount_received = $checkout['sub_total'] + $checkout['shipping']['price'] + $code;
            $payment->payment_method_id = $request->payment_method;
            $payment->code = $code;
            $data->payment()->save($payment);

            foreach($checkout['products'] as $i){
                $cr = Cart::where('variant_id', $i['variant_id'])
                ->where('user_id', $user_id)->first();
                $cr->delete();
            }

            Session::forget('checkout');
            Session::forget('cart');

        }catch(\QueryException $e){
            DB::rollback();
            return back();
        }
        DB::commit();
        return redirect()->route('user.order.payment.show', $data->id);
    }

    public function bayar()
    {
       Config::$serverKey = 'SB-Mid-server-3BbNpq3n04Z_E_4XNcLw5_DP';
       Config::$clientKey = "SB-Mid-client-LI78HrfmB5ouletb";
       Config::$isProduction = false;
       Config::$isSanitized = true;
       Config::$is3ds = true;
    //    return view('coba');
       $items = array(
            array(
                'id'       => 'item1',
                'price'    => 100000,
                'quantity' => 1,
                'name'     => 'Adidas f50'
            ),
            array(
                'id'       => 'item2',
                'price'    => 50000,
                'quantity' => 2,
                'name'     => 'Nike N90'
            )
        );

    //     // Populate customer's billing address
    //     $billing_address = array(
    //         'first_name'   => "Andri",
    //         'last_name'    => "Setiawan",
    //         'address'      => "Karet Belakang 15A, Setiabudi.",
    //         'city'         => "Jakarta",
    //         'postal_code'  => "51161",
    //         'phone'        => "081322311801",
    //         'country_code' => 'IDN'
    //     );

    //     // Create transaction summary
        // $transaction_details = array(
        //     'order_id'    => ,
        //     'gross_amount'  => 200000
        // );

    //     // Token ID from checkout page
    //     $token_id = app('request')->token_id;
    //     // Transaction data to be sent
        $transaction_data = array(
            'payment_type' => 'bank_transfer',
            'bank_transfer'  => array(
                'bank' => 'bca',
                "va_number" => "089656466525",
            ),
            'transaction_details' => $transaction_details,
        );

        // do charge
        $response = Midtrans::charge($transaction_data);
        dd($response);
    }
}
