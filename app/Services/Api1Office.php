<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\API_connection;
use App\Models\Customer;
use App\Services\ApiKiotViet;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class Api1Office
{
    protected $access_token;
    protected $url_API_1office;
    protected $ApiKiotViet;


    public function __construct()
    {
        $api_connection = API_connection::find(1);
        $this->access_token = $api_connection->token;
        $this->url_API_1office = $api_connection->link_connect;
    }

    public function insertCustomer($code, $type, $name, $phones, $group_type_id, $fountain_head_id)
    {
        $LinkEndpoint = $this->url_API_1office . 'api/customer/contact/insert?access_token='.$this->access_token;
        $client = new Client();
        $options = [
            'multipart' => [
                [
                    'name' => 'code',
                    'contents' => $code
                ],
                [
                    'name' => 'type',
                    'contents' => $type
                ],
                [
                    'name' => 'name',
                    'contents' => $name
                ],
                [
                    'name' => 'phones',
                    'contents' => $phones
                ],
                [
                    'name' => 'group_type_id',
                    'contents' => $group_type_id
                ],
                [
                    'name' => 'fountain_head_id',
                    'contents' => $fountain_head_id
                ],
                [
                    'name' => 'desc',
                    'contents' => $code
                ]
            ]
        ];
        $request = new Request('POST', $LinkEndpoint);
        $response = $client->sendAsync($request, $options)->wait();
        return json_decode($response->getBody()->getContents(), true);
    }
    public function updateCustomer($code, $type, $name, $phones, $group_type_id, $fountain_head_id)
    {
        $LinkEndpoint = $this->url_API_1office . 'api/customer/contact/insert?access_token='.$this->access_token;
        $client = new Client();
        $options = [
            'multipart' => [
                [
                    'name' => 'code',
                    'contents' => $code
                ],
                [
                    'name' => 'type',
                    'contents' => $type
                ],
                [
                    'name' => 'name',
                    'contents' => $name
                ],
                [
                    'name' => 'phones',
                    'contents' => $phones
                ],
                [
                    'name' => 'group_type_id',
                    'contents' => $group_type_id
                ],
                [
                    'name' => 'fountain_head_id',
                    'contents' => $fountain_head_id
                ],
                [
                    'name' => 'desc',
                    'contents' => $code
                ]
            ]
        ];
        $request = new Request('POST', $LinkEndpoint);
        $response = $client->sendAsync($request, $options)->wait();
        return json_decode($response->getBody()->getContents(), true);
    }
    public function getKhachHangID($id)
    {
        $LinkEndpoint = $this->url_API_1office . 'api/customer/contact/item?access_token='.$this->access_token.'&code='.$id;
        // Gửi request đến API để lấy danh sách categories
        $client = new Client();
        // dd($LinkEndpoint);
        $response = $client->get($LinkEndpoint);
        return json_decode($response->getBody()->getContents(), true);
    }
}
