<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Sale;
use Auth;

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
        $dataList = User::when($request->search, function($query, $search){
            $query->where('name', 'LIKE', '%' . $search . '%')
            ->orWhere('email', 'LIKE', '%' . $search . '%');
        })
        ->orderBy('id', 'desc')->paginate(6);
 
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
        return view('base::create');
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
