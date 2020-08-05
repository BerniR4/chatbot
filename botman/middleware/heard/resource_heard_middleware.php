<?php

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Interfaces\Middleware\Heard;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class resource_heard_middleware implements Heard {

    public function heard(IncomingMessage $message, $next, BotMan $bot) {
        $message->addExtras('prova', 'provica');
        return $next($message);
    }
}