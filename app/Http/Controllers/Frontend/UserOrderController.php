<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Sale;
use App\Models\SaleLine;
use App\Models\Accounting\Payment;
use App\Models\Accounting\PaymentProof;
use Illuminate\Support\Facades\DB;

use Storage;
class UserOrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $user_id = auth()->guard('web')->user()->id;

        // $data = Sale::with(['line' => function($q){
        //     return $q->with('product');
        // }
        // ])
        // ->where('customer_id', $user_id)
        // ->whereIn('payment_status',)->paginate(4);

        

        $query = Sale::with(['line' => function($q){
            return $q->with('product');
        }])
        ->where('customer_id', $user_id);

        $query->when($request->status == 'berlangsung' || empty($request->status), function ($q) {
            return $q->whereIn('status',  array('pending', 'confirmed', 'delivery'))
            ->whereIn('payment_status', array('pending', 'paid'));
        });

        $query->when($request->status == 'diproses', function ($q) {
            return $q->where('status', 'pending')->where('payment_status', 'paid');
        });

        $query->when($request->status == 'cancel', function ($q) {
            return $q->where('status', 'cancel');
        });

        $query->when($request->status == 'selesai', function ($q) {
            return $q->where('status', 'done');
        });
        
        $data = $query->orderBy('date', 'desc')->paginate(4);

        return Inertia::render('Frontend/User/Order/Index',[
            'dataList' => $data,
        ]);
    }


    public function show($id, Request $request)
    {
        $user_id = auth()->guard('web')->user()->id;

        $data = Sale::with(['line' => function($q){
            return $q->with('product');
        }, 'payment', 'delivery'
        ])
        ->where('id', $id)
        ->where('customer_id', $user_id)->where('is_web', 1)->first();

        return Inertia::render('Frontend/User/Order/Detail',[
            'data' => $data,
        ]);
    }


        /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function Payment(Request $request)
    {
        $user_id = auth()->guard('web')->user()->id;

        $data = Sale::with(['payment' => function($q){
                return $q->with('payment_method');
            },
        ])
        ->where('customer_id', $user_id)
        ->where('is_web', 1)
        ->where('payment_status', 'unpaid')->get();

        return Inertia::render('Frontend/User/Order/Unpaid',[
            'dataList' => $data
        ]);
    }

    public function payment_show($order, Request $request)
    {
        $user_id = auth()->guard('web')->user()->id;

        $query = Payment::select('payment.id as payment_id', 'payment.code', 'payment.amount', 'payment.payment_method_id', 'a.id as sale_id', 'a.ref', 'a.status', 'a.payment_status', 
        'a.grand_total', 'a.shipping_cost', 'a.total', 'a.created_at', 'a.payment_due')
        ->leftJoin('sales as a', 'a.id', '=', 'payment.paymenttable_id')
        ->with(['payment_method' => function($q){
            $q->select('account_payment_methods.*', 'b.account_name', 'b.account_no', 'b.logo')
            ->join('account_bank as b', 'b.id', '=', 'account_payment_methods.bank_id');
        }])
        ->where('a.id', $order)
        ->where('a.customer_id', $user_id);

        $data = $query->first();
        return Inertia::render('Frontend/User/Order/Payment',[
            'order' => $data
        ]);
    }


    public function payment_update(Request $request)
    {
        DB::beginTransaction();
        try{

            $user_id = auth()->guard('web')->user()->id;
            $data = Sale::where('id', $request->order_id)->where('customer_id', $user_id)->first();
            $data->payment_status = 'pending';
            $data->save();
            
            if(!empty($request->file('file'))){
                $payment = Payment::where('paymenttable_type', 'App\Models\Sale')->where('paymenttable_id', $request->order_id)->first();
                $proof = new PaymentProof();
                $proof->payment_id = $payment->id;
                 
                $file_name = $request->order_id.'-'.uniqid() . $request->file('file')->getClientOriginalName();
                Storage::disk('public')->putFileAs('uploads/payment',
                    $request->file('file'),
                    $file_name
                );
                $proof->path = 'uploads/payment/'.$file_name;
                $proof->save();
            }

        }catch(\QueryException $e){
            DB::rollback();
            return back();
        }
        DB::commit();
        return redirect()->route('user.order.show', $data->id);
    }

    public function confirm(Request $request)
    {
        DB::beginTransaction();
        try{

            $user_id = auth()->guard('web')->user()->id;
            $data = Sale::where('id', $request->order_id)->where('customer_id', $user_id)->first();
            $data->status = 'done';
            $data->save();
            
        }catch(\QueryException $e){
            DB::rollback();
            return back();
        }
        DB::commit();
        return redirect()->route('user.order.show', $data->id);
    }


    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function updateProfil(Request $request)
    {
        $rules = [
            'name' => 'required',
        ];

        $pesan = [
            'name.required' => 'Full Name must be filled.',
        ];

        $validator = Validator::make($request->all(), $rules, $pesan);
        if ($validator->fails()){
            return back()->withErrors($validator->errors());
        }else{
            DB::beginTransaction();
            try{
                    $data = User::where('id', auth()->guard('web')->user()->id)->first();
                    $data->name = $request->name;
                    if($data->avatar != $request->avatar){
                        if(Storage::disk('public')->exists($data->avatar))
                        {
                            Storage::disk('public')->delete($data->avatar);
                        }
                        if($request->hasFile('avatar')){
                            $data->avatar = $this->uploadFiles($request->file('avatar'), Str::slug($request->username, '-'));
                        }else{
                            $data->avatar = null;
                        }
                    }
                    $data->save();

                    $data->assignRole($request->role);

            }catch(\QueryException $e){
                DB::rollback();
                return back();
            }
            DB::commit();
            return redirect()->route('admin.profile');
        }
    }

    
    private function uploadFiles($file, $name){
        $file_name = $name. '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        Storage::disk('public')->putFileAs(
            'uploads/customer',
            $file,
            $file_name
        );
        
        return 'uploads/customer/'.$file_name;
    }


    public function sa(Request $request)
    {
        $data = User::select('name', 'email', 'phone', 'avatar')
        ->where('id', auth()->guard('web')->user()->id)->first();

        return Inertia::render('Frontend/User/Order/Index',[
            'data' => $data,
        ]);
    }

}
