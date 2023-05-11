<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use OwenIt\Auditing\Facades\Audit;
use App\Models\Customer;
use Carbon\Carbon;

class CustomersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // dd('Đưa vào hàng đợi lấy dữ liệu khách hàng');
        $result = $this->fetchCustomers();
        if ($result == 1) {
            //dd('Lấy dữ liệu khách hàng thành công');
            $this->job->delete();
        } elseif ($result == 2) {
            // dd('Hiện tại đang là dữ liệu mới nhất');
            $this->job->delete();
        } elseif ($result == 3) {
        //    dd('Có lỗi xảy ra khi lấy dữ liệu khách hàng');
            $this->job->fail();
        }
    }

    public function fetchCustomers($page = 1) {
        $latestCustomer = Customer::orderBy('created_at', 'desc')->orderBy('id', 'desc')->first();
        $currentDateTime = Carbon::now()->format('d/m/Y H:i:s');
        // dd($currentDateTime);
        if($latestCustomer){
        $jsonData = $latestCustomer->created_at;
        // $dataObj = json_decode($jsonData);
        // $carbon_obj = Carbon::createFromFormat('d/m/Y H:i:s', $dataObj->datetime_created);
        $dateCreated = $jsonData->addSecond()->format('d/m/Y H:i:s');
        // dd($dateCreated);
        } else {
            $dateCreated = "01/01/2000 00:00:01";
        }

        $headers = ['Cookie' => 'PHPSESSID=39u5mrdnr0soq1gsbp5bjhfdc8'];

        $response = Http::withHeaders($headers)->get('https://demo.1office.vn/api/customer/customer/gets', [
            'access_token' => '5274176036427b664f2e55695668929',
            'page' => $page,
            'filters' => '[{"datetime_created_from":"'.$dateCreated.'", "datetime_created_to":"'.$currentDateTime.'"}]'
        ]);

        // dd($response->json());

        if ($response->ok() && $response->json()['error']==false) {
            $customers = $response->json()['data'];
            if (count($customers) > 0) {
                foreach ($customers as $customer) {
                    $newCustomer = new Customer;
                    $newCustomer->customer_code = $customer['code'];
                    $newCustomer->data = json_encode($customer);
                    $newCustomer->synced_data = "{'1office':'1','sapo':'0','kiotviet':'0'}";
                    $newCustomer->note = "Lưu dữ liệu từ 1office về";
                    $newCustomer->save();
                }
                // Thực hiện gọi đệ quy để lấy trang tiếp theo nếu cần
                $total_item = $response->json()['total_item'];
                if ($total_item > ($page * 50)) {
                    $nextPage = $page + 1;
                    $this->fetchCustomers($nextPage);
                } else {
                    return 1;
                }
            } else {
                return 2;
            }
        } else {
            return 3;
        }
    }
}
