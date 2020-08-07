<?php

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Interfaces\Middleware\Matching;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class resource_matching_middleware implements Matching {

    public function matching(IncomingMessage $message, $pattern, $regexMatched) {
        $message->addExtras('pattern', $pattern);
        $message->addExtras('regexmatched', $regexMatched);
        return $regexMatched;
    }
}