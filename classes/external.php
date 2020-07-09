<?php

//namespace block_xatbot;

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->dirroot/blocks/xatbot/botman/vendor/autoload.php");

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;

DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);

class external extends external_api {
	
	public static function test_call_parameters() {
		return new external_function_parameters(
			array(
				//new external_single_structure( 
				//	array(
						'driver' 	=> new external_value(PARAM_TEXT, 'test text'),
						'message' 	=> new external_value(PARAM_TEXT, 'message')
							//new external_value(PARAM_TEXT, 'Message'),
				//	)
				//)
			)
		);
	}

	public static function test_call_returns() {
		return new external_value(PARAM_TEXT, 'text');//new external_single_structure(
			//array(
				
				/*'status' => new external_value(PARAM_INT, 'test text'),
				'messages' => new external_multiple_structure(
					new external_single_structure(
						array(
							'type' => new external_value(PARAM_TEXT, 'message type'),
							'text' => new external_value(PARAM_TEXT, 'message text'),
							'attachment' => new external_single_structure(
								array(
									'type' => new external_value(PARAM_TEXT, 'attachment type'),
									'url' => new external_value(PARAM_TEXT, 'attachment url'),
									'title' => new external_value(PARAM_TEXT, 'attachment title'),
								)
							),
							'additionalParameters' => new external_multiple_structure(
								new external_value(PARAM_TEXT, '')
								//new external_single_structure(
								//	array()
								//)
							),
						)
					)
				),*/
			//)
		//);
	}

	public static function test_call($test) {
		global $USER, $COURSE;
		$config = [];
		//$aux = [
		//	0 => array(
		//		'name' => $test,
		//	),
		//];
		global $jajj;
		$jajj = 'caca';
		$botman = BotManFactory::create($config);
		$botman->hears('hola', function($bot) {
			$bot->reply('pringat');
		});
		
		$botman->hears('{name}', function($bot, $asdf) {
			$jajj = $jajj . ' asfasdf';
			$bot->reply('has dit ' . $asdf);
		});
		
		$botman->fallback(function($bot) {
			$jajj = $jajj . 'hola';
			$bot->reply('hola');
		});
		$botman->listen();
		//$aux[0]['name'] = $test . $USER->id . ' xDD' . $COURSE->id . $jajj;
		//return;
		//return $aux;
		//return ' ';
		return $jajj;
	}

}
