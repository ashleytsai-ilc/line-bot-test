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
                $this->dictionaryService = new DictionaryService($this->bot, $event);

                if ($event instanceof TextMessage) {
                    $response = $this->dictionaryService->dictionary();
                }
            }
        }
        
        return $response;
    }

    public function sendText(Request $request)
    {
        $questionKeywords = '是什麼|什麼是|意思|查|解釋';
        
        if (preg_match("/[$questionKeywords]+/u", $request->userText, $matches)) {
            if (preg_match_all('/[A-Za-z]+/i', $request->userText, $matches)) {
                $word = $matches[0];

                $definitions = \App\Definition::where('word', $word)
                                ->select('speech', 'explainTw')
                                ->get();

                $explains = [];
                foreach ($definitions as $definition) {
                    $wordWithSpeech = '['.$definition->speech.']'.$definition->explainTw;
                    if (!in_array($wordWithSpeech, $explains)) {
                        $explains[] = $wordWithSpeech;
                    }
                }

                return json_encode([
                    'type' => 'text',
                    'text' => implode('<br>', $explains)
                ]);
            }
        }
    }
}
