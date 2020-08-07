<?php

require_once 'vendor/autoload.php';

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Incoming\Answer;

class resource_listener_conver extends Conversation {
	
	/** @var string */
	protected $type;

	/** @var string */
	protected $course;

	public function ask_type() {
		
		$question = Question::create(get_string('fullaskresourcetype', 'block_xatbot'))
        ->addButtons([
            Button::create(get_string('pluginname', 'mod_resource'))->value(get_string('pluginname', 'mod_resource')),
			Button::create(get_string('pluginname', 'mod_url'))->value(get_string('pluginname', 'mod_url')),
			Button::create(get_string('buttonall', 'block_xatbot'))->value(get_string('buttonall', 'block_xatbot'))
        ]);

		$this->ask($question, function (Answer $answer) {
			$this->type = null;
			if (strcasecmp($answer->getValue(), get_string('pluginname', 'mod_resource')) == 0) {
				$this->type = 'resource';
			} elseif (strcasecmp($answer->getValue(), get_string('pluginname', 'mod_url')) == 0) {
				$this->type = 'url';
			}

			$this->ask_course();
		});
	}

	public function ask_course() {
		$question = Question::create(get_string('fullaskresourcecourse', 'block_xatbot'))
        ->addButtons([
			Button::create(get_string('buttonall', 'block_xatbot'))->value(get_string('buttonall', 'block_xatbot'))
		]);
		
		$this->ask($question, function (Answer $answer) {
			if (!strcasecmp($answer->getValue(), get_string('buttonall', 'block_xatbot'))) {
				$this->course = $answer->getValue(); 
			}
			$this->say('ha tirat????' . $this->course. $this->type);

		});
	}

	public function run() {
		$this->ask_type();
	}

}
