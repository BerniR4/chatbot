<?php

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Interfaces\Middleware\Received;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class resource_received_middleware implements Received {

    public function received(IncomingMessage $message, $next, BotMan $bot) {
        $message->addExtras('prova', 'provica');
        return $next($message);
    }
}