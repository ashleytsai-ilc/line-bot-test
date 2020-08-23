<?php

namespace App\Services;

use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;

class TemplateService
{
    protected $bot;

    protected $replyToken;

    protected $userText;

    protected $userId;

    public function __construct($bot, $event)
    {
        $this->bot = $bot;
        $this->replyToken = $event->getReplyToken();
        $this->userText = $event->getText();
        $this->userId = $event->getUserId();

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

        case '啟動應聲蟲模式':
            $templateMsg = new TextMessageBuilder('在訊息的最前面加入雙冒號::即可啟動應聲蟲模式');
            break;

        default:
            if (starts_with($this->userText, '::')) {
                $reply = str_replace('::', '', $this->userText);
                $templateMsg = new TextMessageBuilder($reply);
            } else {
                $templateMsg = new TextMessageBuilder('輸入help、?或選單可查詢功能');
            }
            
            break;
        }
        // 藉由replyMessage送出
        $response = $this->bot->replyMessage($this->replyToken, $templateMsg);

        return $response;
    }

    public function carouselTemplate()
    {
        // 卡片的圖像
        $thumbnailImageUrl = 'https://dvblobcdnjp.azureedge.net//Content/ueditor/net/upload1/2019-09/543c23c9-e311-4926-9714-e256773e59de.jpg';

        // 卡片中選項的template
        $bindActionBuilder = new UriTemplateActionBuilder('學員綁定', route('replyAction', ['userId' => $this->userId]));
        // $carActionBuilder = new MessageTemplateActionBuilder('Carousel Template', 'Carousel Template');
        $btnActionBuilder = new MessageTemplateActionBuilder('Buttons Template', 'Buttons Template');
        $confActionBuilder = new MessageTemplateActionBuilder('啟動應聲蟲模式', '啟動應聲蟲模式');

        // 卡片的template
        $templateColumnBuilder = new CarouselColumnTemplateBuilder('This is title', 'This is text', $thumbnailImageUrl, [$bindActionBuilder, $btnActionBuilder, $confActionBuilder]);
        
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
        $templateBuilder = new ButtonTemplateBuilder('This is button title', 'This is button text', $thumbnailImageUrl, [$actionBuilder]);

        $templateMsg = new TemplateMessageBuilder('This is buttons template', $templateBuilder);

        return $templateMsg;
    }

    public function confirmTemplate()
    {
        // 卡片中選項的template
        $yesActionBuilder = new MessageTemplateActionBuilder('yes', 'yes');
        $noActionBuilder = new MessageTemplateActionBuilder('no', 'no');

        // 組合成規定陣列
        $templateBuilder = new ConfirmTemplateBuilder('Are you sure?', [$yesActionBuilder, $noActionBuilder]);

        $templateMsg = new TemplateMessageBuilder('This is confirm template', $templateBuilder);

        return $templateMsg;
    }
}
