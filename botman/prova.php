<?php
namespace Prova;

require_once __DIR__ . '/../../../config.php' ;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Incoming\Answer;

class MyBotCommands extends Conversation {
	public function handle() {
		
		$question = Question::create('Do you need a database?')
        ->fallback('Unable to create a new database')
        ->callbackId('create_database')
        ->addButtons([
            Button::create('Of course')->value('yes'),
            Button::create('Hell no!')->value('no'),
        ]);

		$this->ask($question, function (Answer $answer) {
			// Detect if button was clicked:
			if ($answer->isInteractiveMessageReply()) {
				$selectedValue = $answer->getValue(); // will be either 'yes' or 'no'
				$selectedText = $answer->getText(); // will be either 'Of course' or 'Hell no!'
				$this->say($selectedValue . ' ' . $selectedText);
			}
			$this->say('hola');

		});
	}

	public function run() {
		$this->handle();
	}

}
