<?php

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Incoming\Answer;

class resource_listener_conver extends Conversation {
	public function handle() {
		
		$question = Question::create(get_string('fullaskresourcetype', 'block_xatbot'))
        ->fallback('Unable to create a new database')
        ->callbackId('create_database')
        ->addButtons([
            Button::create(get_string('pluginname', 'mod_resource'))->value(get_string('pluginname', 'mod_resource')),
			Button::create(get_string('pluginname', 'mod_url'))->value(get_string('pluginname', 'mod_url')),
			Button::create('All')->value('All')
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
