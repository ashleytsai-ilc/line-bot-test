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
                    return $response;
                }
            } elseif ($event instanceof OtherEvent) {
                dd(123);
                if ($event instanceof FollowEvent) {
                    $userService = new UserService($this->bot, $event);
                    $userService->create();
                }
            }
        }
    }

    public function sendText(Request $request)
    {
        $userService = new UserService($request->userId);
        $userService->create();

        return response('ok');
    }
}
