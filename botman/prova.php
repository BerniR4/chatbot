<?php
namespace Prova;

require_once(__DIR__ . '/../../../config.php');

class MyBotCommands {
	public function handle($bot) {
		global $USER;
		$bot->reply('Hello World' . $USER->id);
	}
}
