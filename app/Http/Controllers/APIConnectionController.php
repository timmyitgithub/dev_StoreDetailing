<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\API_connection;

class APIConnectionController extends Controller
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

    public function store(Request $request)
{
    $connection = new API_connection();
    $connection->connection_name = $request->input('connection_name');
    $connection->type_auth = $request->input('type_auth');
    $connection->link_connect = $request->input('link_connect');
    $connection->client_id = $request->input('client_id');
    $connection->token = $request->input('token');
    $connection->scopes = $request->input('scopes');
    $connection->retailer = $request->input('retailer');
    $connection->active_checkbox = $request->input('active_checkbox') ? true : false;
    $connection->save();

    return redirect()->route('settings.api')->with('success', 'Thêm kết nối thành công!');
}

    public function update(Request $request, $id)
{
    $item = API_connection::findOrFail($id);
    $item->connection_name = $request->input('connection_name');
    $item->type_auth = $request->input('type_auth');
    $item->client_id = $request->input('client_id');
    $item->link_connect = $request->input('link_connect');
    $item->token = $request->input('token');
    $item->scopes = $request->input('scopes');
    $item->retailer = $request->input('retailer');
    $item->active = $request->has('active_checkbox');

    $item->save();
    return redirect()->route('settings.api')->with('success', 'Cập nhật thông tin '.$item->connection_name.' thành công!');
}

}
