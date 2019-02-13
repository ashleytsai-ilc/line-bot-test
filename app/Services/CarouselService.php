<?php

namespace App\Services;

use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;

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
        // 卡片的圖像
        $thumbnailImageUrl = 'https://scontent.ftpe8-1.fna.fbcdn.net/v/t1.0-9/13567248_1402023999824169_896253512501907636_n.jpg?_nc_cat=109&_nc_ht=scontent.ftpe8-1.fna&oh=61df15a39e3c48cb97411861ffc07c32&oe=5CFC51EA';

        // 卡片中選項的template
        $actionBuilders = new MessageTemplateActionBuilder('This is label', 'This is action text');
        
        // 卡片的template
        $templateColumnBuilder = new CarouselColumnTemplateBuilder('This is title', 'This is text', $thumbnailImageUrl, [$actionBuilders, $actionBuilders]);
        
        // 建立多張卡片
        $templateBuilder = new CarouselTemplateBuilder([$templateColumnBuilder, $templateColumnBuilder, $templateColumnBuilder]);
        
        // 整個輪動卡片組合成規定陣列
        $templateMsg = new TemplateMessageBuilder('aaa', $templateBuilder);

        // 藉由replyMessage送出
        $response = $this->bot->replyMessage($this->replyToken, $templateMsg);
        
        return $response;
    }
}
