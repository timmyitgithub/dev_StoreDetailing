<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Pusher\Pusher;
use App\Models\API_connection;
use App\Models\Customer;
use App\Services\ApiKiotViet;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use App\Notifications\TestNotification;
use App\Models\User;
use Carbon\Carbon;

class Send_Notification
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

    public function PushNotification($title, $content)
    {

        $user = User::find(1); // id của user mình đã đăng kí ở trên, user này sẻ nhận được thông báo
        $data = [];
        $data['created_at'] = now();
        $data['title'] = $title;
        $data['content'] = $content;
        $user->notify(new TestNotification($data));
        $options = array(
            'cluster' => 'ap1',
            'encrypted' => true
        );

        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        $pusher->trigger('NotificationEvent', 'send-message', $data);
        // return json_decode($response->getBody()->getContents(), true);
    }
}
