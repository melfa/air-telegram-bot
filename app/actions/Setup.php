<?php
namespace air\app\actions;

use Telegram\Bot\Api;
use air\app\telegram\AirCommand;
use air\app\telegram\StartCommand;

class Setup extends Action
{

    public function run()
    {
        $telegram = new Api('149083827:AAFPrapXL65wWplM6NJKda36wcBid9ivZho', true);
        $telegram->setWebhook(['url' => 'https://e22b93c1.ngrok.io/telegraf']);
    }

}
