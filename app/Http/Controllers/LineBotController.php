<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class LineBotController extends Controller
{
    protected $httpClient;

    protected $bot;

    public function __construct()
    {
        $this->httpClient = new CurlHTTPClient(env('LINEBOT_TOKEN'));
        $this->bot = new LINEBot($this->httpClient, 
            ['channelSecret' => env('LINEBOT_SECRET')]
        );
    }

    public function replyMessage()
    {
        $textMessageBuilder = new TextMessageBuilder('hello');
        $response = $this->bot->replyMessage('<replyToken>', $textMessageBuilder);

        dd($response->getRawBody());

        return response()->json('ok');
    }
}
