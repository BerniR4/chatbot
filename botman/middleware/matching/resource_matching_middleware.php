<?php

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Interfaces\Middleware\Matching;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class resource_matching_middleware implements Matching {

    public function matching(IncomingMessage $message, $pattern, $regexMatched) {

        preg_match('/' . $pattern . '/i', $message->getText(), $output);

        return $regexMatched && strlen($output[1]) > 0 ;
    }
}