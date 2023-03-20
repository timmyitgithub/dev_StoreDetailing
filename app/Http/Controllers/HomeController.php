<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\API_connection;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('admin.home');
    }

    public function settings_api()
    {
        $API_connection = API_connection::all();
        return view('admin.settings_api', compact('API_connection'));
    }
}
