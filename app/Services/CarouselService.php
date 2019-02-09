<?php

namespace App\Services;

use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use function GuzzleHttp\json_encode;

class CarouselService
{
    protected $bot;

    protected $replyToken;

    public function __construct($bot, $event)
    {
        $this->bot = $bot;
        $this->replyToken = $event->getReplyToken();
    }

    public function carouselTemplate()
    {
        $thumbnailImageUrl = 'https://scontent.ftpe8-1.fna.fbcdn.net/v/t1.0-9/13567248_1402023999824169_896253512501907636_n.jpg?_nc_cat=109&_nc_ht=scontent.ftpe8-1.fna&oh=61df15a39e3c48cb97411861ffc07c32&oe=5CFC51EA';

        $actionBuilders = new MessageTemplateActionBuilder('This is label', 'This is action text');
        
        $templateColumnBuilder = new CarouselColumnTemplateBuilder('This is title', 'This is text', $thumbnailImageUrl, $actionBuilders);
        dd(123);

        $templateBuilder = new CarouselTemplateBuilder($templateColumnBuilder);
        $templateMsg = new TemplateMessageBuilder('This is a carousel template', $templateBuilder);

        $result = json_encode($templateMsg->buildMessage());
        $response = $this->bot->replyText($this->replyToken, $result);

        return $response;
    }
}
