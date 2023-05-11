<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Services\ApiKiotViet;
use OwenIt\Auditing\Facades\Audit;
use App\Services\Send_Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class KiotVietTo1OfficeController extends Controller
{

    protected $ApiKiotViet;
    protected $Send_Notification;

    public function __construct(ApiKiotViet $ApiKiotViet, Send_Notification $Send_Notification)
    {
        $this->middleware('auth');
        $this->ApiKiotViet = $ApiKiotViet;
        $this->Send_Notification = $Send_Notification;
    }

    public function index()
    {
        $customers = Customer::paginate(20);
        return view('admin.customers.index', compact('customers'));
    }
    public function create()
    {
        $categories = $this->ApiKiotViet->InsertAllKhachHang(1,20);
        switch ($categories) {
            case 1:
                return redirect()->route('customers.index')->with('success', 'Bạn sẽ nhận được thông báo khi hoàn tất');
                break;
            case 0:
                return redirect()->route('customers.index')->with('success', 'Không có khách hàng mới nào cần đồng bộ');
                break;
        }
        // $customers = Customer::all();
        // return view('admin.customers.index', compact('customers'));
    }
    public function re_sync($id)
    {
        $this->ApiKiotViet->Re_syncKhachHang($id);
        return back()->with('success', 'Đồng Bộ thành Công Khách Hàng ' . $id);
    }
    public function handle(Request $request)
    {
        $data = $request->json()->all();
        $this->ApiKiotViet->InsertKhachHang("JJ009",0,json_encode($data),"","111");
        // Trả về response thành công
        return response()->json(['success' => true]);
    }
}
