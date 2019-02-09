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
                    
                    if (preg_match("/[$questionKeywords]+/u", $event->getText())) {
                        $this->carouselService = new CarouselService($this->bot, $event);
                        dd(123);

                        $response = $this->carouselService->carouselTemplate();
                        
                    } else {
                        $this->dictionaryService = new DictionaryService($this->bot, $event);

                        $response = $this->dictionaryService->dictionary();
                    }
                }
            }
        }
        
        return $response;
    }

    public function sendText(Request $request)
    {
        Log::info('sendText...');
        return 'sendText';
    }
}
