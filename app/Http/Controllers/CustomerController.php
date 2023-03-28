<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Http;


class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        $headers = ['Cookie' => 'PHPSESSID=39u5mrdnr0soq1gsbp5bjhfdc8'];

        $response = Http::withHeaders($headers)->get('https://demo.1office.vn/api/customer/customer/gets', [
            'access_token' => '95691939364225fa674741672640687',
            'page' => 1,
            'filters' => '[{"date_created_from":"01/01/2022", "date_created_to":"31/12/2022"}]'
        ]);

        if ($response->ok()) {
            $customers = $response->json()['data'];
            $data = [];
            foreach ($customers as $customer) {
                $data[] = [            'customer_code' => $customer['code'],
                    'data' => json_encode($customer),
                    'synced_data' => "{'1office':'1','sapo':'0','kiotviet':'0'}",
                    'note' => "Lưu dữ liệu từ 1office về",
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            Customer::insert($data);
            return redirect()->route('customers.index');
        }
    }

    public function store(Request $request)
    {
        $customer = new Customer();
        $customer->customer_code = $request->input('customer_code');
        $customer->synced_data = $request->input('synced_data');
        $customer->data = $request->input('data');
        $customer->note = $request->input('note');
        $customer->save();

        return redirect()->route('customers.index');
    }

    public function edit($id)
    {
        $customer = Customer::find($id);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);
        $customer->customer_code = $request->input('customer_code');
        $customer->synced_data = $request->input('synced_data');
        $customer->data = $request->input('data');
        $customer->note = $request->input('note');
        $customer->save();

        return redirect()->route('customers.index');
    }

    public function destroy($id)
    {
        $customer = Customer::find($id);
        $customer->delete();

        return redirect()->route('customers.index');
    }
}
