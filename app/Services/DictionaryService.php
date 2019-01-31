<?php 
namespace App\Services;

use App\Word;
use App\Definition;
use App\Sentence;

class DictionaryService
{
    protected $bot;

    protected $event;

    protected $userText;

    public function __construct($bot, $event)
    {
        $this->bot = $bot;
        $this->event = $event;
        $this->userText = $this->event->getText();
    }

    /**
     * This is for test
     *
     * @return [json] $res
     */
    public function replySameMsg()
    {
        $res = $this->bot->replyText($this->event->getReplyToken(), $this->userText);

        return $res;
    }

    public function dictionary()
    {
        $questionKeywords = ['是什麼', '什麼是', '意思', '查', '解釋'];

        foreach ($questionKeywords as $keyword) {
            if (preg_match_all('/[A-Za-z]+/i', $this->userText, $matches)) {
                $response = $this->bot->replyText($this->event->getReplyToken(), '進到if了');

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

                $replyText = [
                    'text' => implode(' ', $explains)
                ];

                $response = $this->bot->replyText($this->event->getReplyToken(), implode(' ', $explains));
            }
        }

        return $response;
    }
}

