<?php 
namespace App\Services;

use App\Word;
use App\Definition;
use App\Sentence;

class DictionaryService
{
    protected $bot;

    protected $event;

    public function __construct($bot, $event)
    {
        $this->bot = $bot;
        $this->event = $event;
    }

    public function replySameMsg()
    {
        $replyText = $this->event->getText();
        $res = $this->bot->replyText($this->event->getReplyToken(), $replyText);

        return $res;
    }
}

