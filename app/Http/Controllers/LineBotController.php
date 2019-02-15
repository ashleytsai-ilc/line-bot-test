<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use App\Services\DictionaryService;
use App\Services\TemplateService;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot\Event\FollowEvent;
use App\Services\UserService;

class LineBotController extends Controller
{
    protected $httpClient;

    protected $bot;

    protected $dictionaryService;

    public function __invoke(ServerRequestInterface $req, ResponseInterface $res)
    {
        $this->httpClient = new CurlHTTPClient(env('LINEBOT_TOKEN'));
        $this->bot = new LINEBot($this->httpClient, 
            ['channelSecret' => env('LINEBOT_SECRET')]
        );

        $signature = $req->getHeader('X-Line-Signature');
        if (empty($signature)) {
            return $res->withStatus(400, 'Bad Request');
        }

        // Check request with signature and parse request
        try {
            $events = $this->bot->parseEventRequest($req->getBody(), $signature[0]);
        } catch (InvalidSignatureException $e) {
            return $res->withStatus(400, 'Invalid signature');
        } catch (InvalidEventRequestException $e) {
            return $res->withStatus(400, "Invalid event request");
        }

        foreach ($events as $event) {
            if ($event instanceof MessageEvent) {
                if ($event instanceof TextMessage) {
                    $response = new TemplateService($this->bot, $event);
                }
            } elseif ($event instanceof OtherEvent) {
                if ($event instanceof FollowEvent) {
                    $userService = new UserService($this->bot, $event);
                    $userService->create();
                }
            }
        }
        
        return $response;
    }

    public function sendText(Request $request)
    {
        // 卡片的圖像
        $thumbnailImageUrl = 'https://i.ytimg.com/vi/GNnM-LSa5OQ/maxresdefault.jpg';

        // 卡片中選項的template
        $actionBuilder = new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder('只有一個按鈕', '只有一個按鈕');
        
        // 組合成規定陣列
        $templateBuilder = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder('This is button title', 'This is button text', $thumbnailImageUrl, [$actionBuilder]);
        
        $templateMsg = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder('This is buttons template', $templateBuilder);

        return response()->json($templateMsg->buildMessage());
    }
}
