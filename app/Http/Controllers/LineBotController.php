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
use App\Services\CarouselService;
use Illuminate\Support\Facades\Log;

class LineBotController extends Controller
{
    protected $httpClient;

    protected $bot;

    protected $dictionaryService;

    protected $carouselService;

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
                    $questionKeywords = 'help|?|選單';
                    
                    if (preg_match_all("/[$questionKeywords]+/u", $event->getText())) {
                        $carouselService = new CarouselService($this->bot, $event);

                        $response = $carouselService->carouselTemplate();
                    } else {
                        $dictionaryService = new DictionaryService($this->bot, $event);

                        $response = $dictionaryService->dictionary();
                    }
                }
            }
        }
        
        return $response;
    }

    public function sendText(Request $request)
    {
        $thumbnailImageUrl = 'https://scontent.ftpe8-1.fna.fbcdn.net/v/t1.0-9/13567248_1402023999824169_896253512501907636_n.jpg?_nc_cat=109&_nc_ht=scontent.ftpe8-1.fna&oh=61df15a39e3c48cb97411861ffc07c32&oe=5CFC51EA';

        $actionBuilders = new \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder('This is label', 'This is action text');
        
        $templateColumnBuilder = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder('This is title', 'This is text', $thumbnailImageUrl, [$actionBuilders, $actionBuilders]);
        
        $templateBuilder = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder([$templateColumnBuilder, $templateColumnBuilder, $templateColumnBuilder]);
        

        $templateMsg = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder('This is a carousel template', $templateBuilder);

        // $result = json_encode($templateMsg->buildMessage());
        
        return response()->json($templateMsg->buildMessage());

    }
}
