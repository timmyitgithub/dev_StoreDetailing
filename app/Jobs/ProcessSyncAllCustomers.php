<?php

namespace App\Jobs;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Api1Office;
use App\Services\ApiKiotViet;
use App\Services\Send_Notification;
use App\Jobs\SyncAllCustomers;
use Hamcrest\Core\HasToString;

class ProcessSyncAllCustomers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $p;
    protected $ps;
    protected $_count;
    protected $customer_total;
    protected $api1Office;
    protected $apiKiotViet;
    protected $sendNotification;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $p, int $ps)
    {
        $api1Office = new Api1Office();
        $apiKiotViet = new ApiKiotViet($api1Office, new Send_Notification());
        $sendNotification = new Send_Notification();
        $this->api1Office = $api1Office;
        $this->apiKiotViet = $apiKiotViet;
        $this->sendNotification = $sendNotification;
        $this->p = $p;
        $this->ps = $ps;
        $this->customer_total = 0;
        $this->_count = 0;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $currentItem = $this->p;
        $customer = $this->apiKiotViet->getLsKhachHang($currentItem, $this->ps);
        $datas = $customer['data'];
        $customer_total = $customer['total'];
        $this->customer_total = $customer_total;
        $this->_count = count($datas);
        $customerall = Customer::all()->count();
        $pageSize = $customer['pageSize'];
        if ($customer_total > 0) {
            foreach ($datas as $dt){
                SyncAllCustomers::dispatch($dt);
            }
            $currentItem = $pageSize + $currentItem;
            if ($customer_total >= $currentItem) {
                $this->apiKiotViet->InsertAllKhachHang($currentItem, $pageSize);
            } else {
                return 1;
            }
        } else {
            $this->sendNotification->PushNotification("Đồng bộ khách hàng thành công!", "Đồng bộ KH");
            return 0;
        }

        // Thực hiện xử lý đồng bộ dữ liệu khách hàng ở đây
        $success = true;

        if ($success) {
            // Nếu thực hiện thành công, gọi method onSuccess()
            $this->onSuccess();
        } else {
            // Nếu thực hiện thất bại, gọi method onFailure()
            $this->onFailure();
        }
    }
    public function onSuccess()
    {
        $this->sendNotification->PushNotification($this->_count . " Đã được đồng bộ Thành Công","Đồng Bộ Thành Công" );
    }

    public function onFailure()
    {
        $this->sendNotification->PushNotification("Đồng bộ Thất Bại","Đồng Bộ Thất Bại" );
    }

}
