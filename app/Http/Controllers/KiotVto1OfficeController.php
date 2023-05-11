<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
// use Illuminate\Support\Facades\Http;
// use OwenIt\Auditing\Facades\Audit;
// use App\Models\API_connection;
// use Carbon\Carbon;
// use App\Jobs\CustomersJob;
// use App\Models\User;
// use Pusher\Pusher;
// use App\Notifications\TestNotification;
use App\Services\ApiKiotViet;
// use App\Services\Api1Office;

class KiotVto1OfficeController extends Controller
{
    protected $ApiKiotViet;
    public function __construct(ApiKiotViet $ApiKiotViet)
    {
        $this->middleware('auth');
        $this->ApiKiotViet = $ApiKiotViet;
    }

    public function index()
    {
        $customers = Customer::all();
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        $categories = $this->ApiKiotViet->InsertAllKhachHang(1,100);
        switch ($categories) {
            case 1:
                return redirect()->route('customers.index')->with('success', 'Đồng bộ dữ liệu thành công');
                break;
            case 0:
                return redirect()->route('customers.index')->with('success', 'Không có khách hàng mới nào cần đồng bộ');
                break;
        }
        // $customers = Customer::all();
        // return view('admin.customers.index', compact('customers'));
    }

}
