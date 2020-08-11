<?php

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Interfaces\Middleware\Matching;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class resource_matching_middleware implements Matching {

    public function matching(IncomingMessage $message, $pattern, $regexMatched) {

        preg_match('/' . $pattern . '/i', $message->getText(), $output);

        /*$message->addExtras('pattern', $pattern);
        $message->addExtras('regexmatched', $regexMatched);
        $message->addExtras('bool', $message->getText());
        $message->addExtras('test', $aux);
        $message->addExtras('jajj', $output[1]);*/
        return $regexMatched && strlen($output[1]) > 0 ;
    }
}