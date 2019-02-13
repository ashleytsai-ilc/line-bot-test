<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;

class EchoBotController extends Controller
{
    protected $bot;

    protected $userText;

    protected $replyToken;

    public function __construct($bot, $event)
    {
        $this->bot = $bot;
        $this->userText = $event->getText();
        $this->replyToken = $event->getReplyToken();
    }

    /**
     * This is for test
     *
     * @return [json] $res
     */
    public function replyMsg()
    {
        $res = $this->bot->replyText($this->replyToken, '你點了'.$this->userText);

        return $res;
    }
}
