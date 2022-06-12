<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
class ShipperController extends Controller
{

    private $base_url;
    private $api_key;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->base_url = 'https://merchant-api-sandbox.shipper.id/v3';
        $this->api_key = 'qIytwbHtqTmG1CeDfa7YbpKxpyHPJwtdNu3pqvBi9mx7by2hZ4SkRBWnkKhgtjbg';
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSubUrban(Request $request)
    {
        $client = new Client();
        $response = $client->get($this->base_url . '/location', 
        [
            'headers' => [
                'Accept' => 'application/json',
                'X-API-Key' => $this->api_key,
            ],
            'query' => [
                'adm_level' => 5,
                'keyword' => $request->keyword,
                'limit' => 30,
            ]
        ]);
        $res = json_decode($response->getBody());
        return response()->json($res);
    }
}
