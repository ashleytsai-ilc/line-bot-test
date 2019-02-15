<?php
namespace App\Services;

use App\User;

class UserService
{
    protected $bot;

    protected $userId;

    public function __construct($bot, $event)
    {
        $this->bot = $bot;
        $this->userId = $event->getUserId();
    }

    public function create()
    {
        User::create([
            'line_id' => $this->userId
        ]);
    }
}
