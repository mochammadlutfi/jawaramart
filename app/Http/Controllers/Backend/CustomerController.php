<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Sale;
use Auth;
use DB;
use Illuminate\Support\Facades\Hash;
class CustomerController extends Controller
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
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $dataList = User::withCount(['sale' => function($q){
            $q->select(DB::raw('coalesce(SUM(grand_total),0) as paidsum'));
        },])
        ->when($request->search, function($query, $search){
            $query->where('name', 'LIKE', '%' . $search . '%')
            ->orWhere('email', 'LIKE', '%' . $search . '%');
        })
        ->orderBy('id', 'desc')->paginate(20);
 
        return Inertia::render('Backend/Customer/index', [
            'dataList' => $dataList
        ]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $this->import();
        // return view('base::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $data = User::where('id', $id)->first();


        $sales = Sale::where('customer_id', $id)->withCount(['line'])
        ->orderBy('date', 'DESC')->get(10);

        return Inertia::render('Backend/Customer/Show', [
            'data' => $data,
            'sales' => $sales,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('base::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

    
    public function import(){
        $filepath = public_path('customer.csv');
        // Reading file
        $file = fopen($filepath, "r");
        $importData_arr = array(); // Read through the file and store the contents as an array
        $i = 0;
        //Read the contents of the uploaded file 
        while (($filedata = fgetcsv($file, 1000, ";")) !== FALSE) {
            $num = count($filedata);
            if ($i == 0) {
            $i++;
            continue;
            }
            for ($c = 0; $c < $num; $c++) {
                $importData_arr[$i][] = $filedata[$c];
            }
            $i++;
        }
        fclose($file);
        
        $j = 0;
        $no = 1;
        // dd($importData_arr);
        DB::beginTransaction();
        try{
            foreach ($importData_arr as $importData) {
                $nip = $importData[0];
                $name = $importData[1];
                $email = $importData[2];
                $password = $importData[3];
                $j++;

                $user = new User();
                $user->name = $name;
                $user->email = $email;
                $user->nip = $nip;
                $user->password = Hash::make($password);
                $user->save();
            }

        }catch(\QueryException $e){
            DB::rollback();
            dd($e);
        }
        DB::commit();
        echo 'done';
    }

    
    public function data(Request $request)
    {
        $keyword = $request->q;
        $data = User::select('id', 'name', 'avatar', 'anggota_id')
        ->when($request->q, function($query, $search){
            $query->where('name', 'LIKE', '%' . $search . '%')
            ->orWhere('email', 'LIKE', '%' . $search . '%');
        })
        ->orderBy('id', 'ASC')->get();
        
        return response()->json($data);
    }
}
