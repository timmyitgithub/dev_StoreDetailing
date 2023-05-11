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
use PHPUnit\Framework\Constraint\Count;

class SyncAllCustomers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $api1Office;
    protected $apiKiotViet;
    protected $sendNotification;

    /**
     * Create a new job instance.
     *
     * @param Api1Office $api1Office
     * @param ApiKiotViet $apiKiotViet
     * @param Send_Notification $sendNotification
     * @param array $data
     *
     * @return void
     */
    public function __construct($data)
    {
        $api1Office = new Api1Office();
        $apiKiotViet = new ApiKiotViet($api1Office, new Send_Notification());
        $sendNotification = new Send_Notification();
        $this->api1Office = $api1Office;
        $this->apiKiotViet = $apiKiotViet;
        $this->sendNotification = $sendNotification;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $customer = Customer::where('customer_code', $this->data['code'])->first();
        $this->apiKiotViet->Re_syncKhachHang($this->data['code']);

        $success = true;

        if (!$success) {
            // Nếu thực hiện thành công, gọi method onSuccess()

            $this->onFailure();
        }
        //  else {
        //     // Nếu thực hiện thất bại, gọi method onFailure()
        //     $this->onSuccess();
        // }
    }
    public function onSuccess()
    {
        $this->sendNotification->PushNotification("Khách hàng ". $this->data['name'] . " Đã được đồng bộ Thành Công","Khách hàng ". $this->data['code'] . "Đã được đồng bộ thành công" );
    }

    public function onFailure()
    {
        $this->sendNotification->PushNotification("Khách hàng ". $this->data['name'] . " đồng bộ Thất Bại","Khách hàng ". $this->data['code'] . "Đã được đồng bộ thành công" );
    }

}
