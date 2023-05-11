<?php
namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use App\Models\API_connection;
use App\Models\Customer;
// use App\Services\Api1Office;
use App\Services\Send_Notification;
use OwenIt\Auditing\Facades\Audit;
use App\Jobs\ProcessSyncAllCustomers;
class ApiKiotViet
{
    protected $url_token;
    protected $clientId;
    protected $clientSecret;
    protected $scopes;
    protected $accessToken;
    protected $retailer;
    protected $url_api;
    protected $Api1Office;
    protected $Send_Notification;


    public function __construct(Api1Office $Api1Office, Send_Notification $Send_Notification)
    {
        $api_connection = API_connection::find(2);
        $this->url_token = $api_connection->link_connect;
        $this->clientId = $api_connection->client_id;
        $this->clientSecret = $api_connection->token;
        $this->scopes = $api_connection->scopes;
        $this->retailer = $api_connection->retailer;
        $this->url_api = "https://public.kiotapi.com";
        $this->accessToken = Cache::get('access_token');
        $this->Api1Office = $Api1Office;
        $this->Send_Notification = $Send_Notification;
    }

    protected function requestAccessToken()
    {
        $tokenEndpoint = $this->url_token;

        // Gửi request để yêu cầu access token mới
        $client = new Client();
        $response = $client->post($tokenEndpoint, [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scopes'=>$this->scopes,
            ],
        ]);

        // Lưu trữ access token và thời gian hết hạn vào cache
        $tokenData = json_decode($response->getBody()->getContents(), true);
        $this->accessToken = $tokenData['access_token'];
        $expiresIn = $tokenData['expires_in'];
        Cache::put('access_token', $this->accessToken, $expiresIn / 60);

        return $this->accessToken;
    }

    public function getLsKhachHang($page,$pageSize)
    {
        $categoriesEndpoint = $this->url_api . '/customers?pageSize=' . $pageSize .'&currentItem=' . $page;

        // Nếu chưa có access token hoặc access token đã hết hạn, yêu cầu access token mới
        if (!$this->accessToken) {
            $this->accessToken = $this->requestAccessToken();
        }

        // Gửi request đến API để lấy danh sách categories
        $client = new Client();
        $response = $client->get($categoriesEndpoint, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Accept' => 'application/json',
                'Retailer'=> $this->retailer,
                'currentItem'=> $page,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getDetailsKhachHangID($id)
    {
        $categoriesEndpoint = $this->url_api . '/customers/' . $id;

        // Nếu chưa có access token hoặc access token đã hết hạn, yêu cầu access token mới
        if (!$this->accessToken) {
            $this->accessToken = $this->requestAccessToken();
        }

        // Gửi request đến API để lấy danh sách categories
        $client = new Client();
        $response = $client->get($categoriesEndpoint, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Accept' => 'application/json',
                'Retailer'=> $this->retailer,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getDetailsKhachHangCODE($code)
    {
        $categoriesEndpoint = $this->url_api . '/customers/code/' . $code;

        // Nếu chưa có access token hoặc access token đã hết hạn, yêu cầu access token mới
        if (!$this->accessToken) {
            $this->accessToken = $this->requestAccessToken();
        }

        // Gửi request đến API để lấy danh sách categories
        $client = new Client();
        $response = $client->get($categoriesEndpoint, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Accept' => 'application/json',
                'Retailer'=> $this->retailer,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function InsertKhachHang($customer_code, $synced_data, $data, $note, $customer_code_1Office)
    {
        $newCustomer = new Customer;
        $newCustomer->customer_code = $customer_code;
        $newCustomer->data = $data;
        $newCustomer->synced_data = $synced_data;
        $newCustomer->note = $note;
        $newCustomer->customer_code_1Office = $customer_code_1Office;
        $newCustomer->save();
    }

    public function Update_KhachHang($Customer, $customer_code, $synced_data, $data, $note, $customer_code_1Office)
    {
        $Customer->customer_code = $customer_code;
        $Customer->data = $data;
        $Customer->synced_data = $synced_data;
        $Customer->note = $note;
        $Customer->customer_code_1Office = $customer_code_1Office;
        $Customer->save();
    }

    public function convertType($type){
        switch($type){
            case 0 : return "Cá nhân";
            case 1 : return "Tổ Chức";
        }
    }

    public function InsertAllKhachHang($p, $ps)
    {
        $this->Send_Notification->PushNotification("Khách hàng đang được đồng bộ, bạn sẽ nhận được thông báo khi hoàn tất", "Đồng bộ KH");

        ProcessSyncAllCustomers::dispatch($p, $ps);
        return 1;
        // $currentItem = $p;
        // $customer = $this->getLsKhachHang($currentItem, $ps);
        // $datas = $customer['data'];
        // $customer_total = $customer['total'];
        // $customerall = Customer::all()->count();
        // $pageSize = $customer['pageSize'];
        // if ($customer_total > 0) {
        //     ProcessSyncAllCustomers::dispatch($datas,$customer_total, $this->Api1Office, $this->Api1Office, $this->Send_Notification);
        //     $currentItem = $pageSize + $currentItem;
        //     if ($customer_total >= $currentItem) {
        //         $this->InsertAllKhachHang($currentItem, $pageSize);
        //     } else {
        //         return 1;
        //     }
        //     $this->Send_Notification->PushNotification("Có ".$customer_total. " khách hàng đang được đồng bộ, bạn sẽ nhận được thông báo khi hoàn tất", "Đồng bộ KH");
        // } else {
        //     $this->Send_Notification->PushNotification("Đồng bộ khách hàng thành công!", "Đồng bộ KH");
        //     return 0;
        // }
    }
    public function Re_syncKhachHang($id)
    {
        $data = $this->getDetailsKhachHangCODE($id);
        $customer = Customer::where('customer_code', $data['code'])->first();
        if ($customer) {
            $customerDetail = $this->getDetailsKhachHangCODE($data['code']);
            $customerIn1Office = $this->Api1Office->getKhachHangID($customer->customer_code_1Office);
            $codeLastInsert="";
            if(!isset($customerIn1Office['data'])){
                $addcustomer = $this->Api1Office->InsertCustomer($data['code'], $this->convertType($data['type']), $data['name'], $data['contactNumber'], $data['groups'], "KiotViet");
                $codeLastInsert = $addcustomer['data']['code'];
            }else{
                $code = $customerIn1Office['data']['code'];
                $addcustomer = $this->Api1Office->InsertCustomer($code, $this->convertType($data['type']), $data['name'], $data['contactNumber'], $data['groups'], "KiotViet");
            }
            $this->Update_KhachHang($customer,$customerDetail['code'], 1, json_encode($customerDetail), "Nguồn từ KiotViet", $codeLastInsert);
        }
    }
    public function Hook_syncKhachHang($id)
    {
        $data = $this->getDetailsKhachHangCODE($id);
        $addcustomer = $this->Api1Office->InsertCustomer($data['code'], $this->convertType($data['type']), $data['name'], $data['contactNumber'], $data['groups'], "KiotViet");
        $codeLastInsert = $addcustomer['data']['code'];
        $this->InsertKhachHang($data['code'], 1, json_encode($data), "Nguồn từ KiotViet", $codeLastInsert);

    }
}
