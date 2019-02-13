<?php

namespace App\Services;

use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;

class TemplateService
{
    protected $bot;

    protected $replyToken;

    protected $userText;

    public function __construct($bot, $event)
    {
        $this->bot = $bot;
        $this->replyToken = $event->getReplyToken();
        $this->userText = $event->getText();

        switch ($this->userText) {
        case 'help':
        case '?':
        case '選單':
        case 'Carousel Template':
            $templateMsg = $this->carouselTemplate();
            break;
            
        case 'Buttons Template':
            $templateMsg = $this->buttonTemplate();
            break;
        
        case 'Confirm Template':
            $templateMsg = $this->confirmTemplate();
            break;

        default:
            $templateMsg = $this->default();
            break;
        }
        // 藉由replyMessage送出
        $response = $this->bot->replyMessage($this->replyToken, $templateMsg);

        return $response;
    }

    public function carouselTemplate()
    {
        // 卡片的圖像
        $thumbnailImageUrl = 'https://scontent.ftpe8-1.fna.fbcdn.net/v/t1.0-9/13567248_1402023999824169_896253512501907636_n.jpg?_nc_cat=109&_nc_ht=scontent.ftpe8-1.fna&oh=61df15a39e3c48cb97411861ffc07c32&oe=5CFC51EA';

        // 卡片中選項的template
        $carActionBuilder = new MessageTemplateActionBuilder('Carousel Template', 'Carousel Template');
        $btnActionBuilder = new MessageTemplateActionBuilder('Buttons Template', 'Buttons Template');
        $confActionBuilder = new MessageTemplateActionBuilder('Confirm Template', 'Confirm Template');

        // 卡片的template
        $templateColumnBuilder = new CarouselColumnTemplateBuilder('This is title', 'This is text', $thumbnailImageUrl, [$carActionBuilder, $btnActionBuilder, $confActionBuilder]);
        
        // 建立多張卡片
        $templateBuilder = new CarouselTemplateBuilder([$templateColumnBuilder, $templateColumnBuilder, $templateColumnBuilder]);
        
        // 整個輪動卡片組合成規定陣列
        $templateMsg = new TemplateMessageBuilder('this is carousel template', $templateBuilder);
        
        return $templateMsg;
    }

    public function buttonTemplate()
    {
        // 卡片的圖像
        $thumbnailImageUrl = 'https://i.ytimg.com/vi/GNnM-LSa5OQ/maxresdefault.jpg';

        // 卡片中選項的template
        $actionBuilder = new MessageTemplateActionBuilder('只有一個按鈕', '只有一個按鈕');

        // 組合成規定陣列
        $templateMsg = new ButtonTemplateBuilder('This is title'[$actionBuilder]);

        return $templateMsg;
    }

    public function confirmTemplate()
    {
        // 卡片中選項的template
        $yesActionBuilder = new MessageTemplateActionBuilder('yes', 'yes');
        $noActionBuilder = new MessageTemplateActionBuilder('no', 'no');

        // 組合成規定陣列
        $templateMsg = new ConfirmTemplateBuilder('Are you sure?', [$yesActionBuilder, $noActionBuilder]);

        return $templateMsg;
    }

    public function default()
    {
        return [
            'type' => 'text',
            'text' => '輸入help，?或選單可查詢功能'
        ];
    }
}