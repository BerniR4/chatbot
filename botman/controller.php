<?php

require_once 'vendor/autoload.php';
require_once __DIR__ . '/../../../config.php' ;
//require_once __DIR__ . '/../../../lib/moodlelib.php';

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Messages\Attachments\File;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use App\Http\Controllers\BotManController;

include 'prova.php';
include __DIR__ . '/functionalities/resource_listener.php';

$config = [
	// Your driver-specific configuration
	// "telegram" => [
	//    "token" => "TOKEN"
	// ]
];

// Load the driver(s) you want to use
DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);

// Create an instance
$botman = BotManFactory::create($config);

// Give the bot something to listen for.
$botman->hears('Hello', function ($bot) {
	$bot->reply('Bones! Sóc LSBot, un Xatbot que t\'ajudarà a cercar diferents recursos.');
	$bot->reply('Per cercar un recurs, utilitza la paraula clau "Recurs", seguit d\'allò que vulguis cercar. Per exemple: "Recurs prova"');
});

$botman->hears('Attachment .*', function ($bot) {
	$attachment = new File('..\/blocks\/xatbot\/block_xatbot.php', [
		'custom_payload' => true,
	]);
	$message = OutgoingMessage::create('Aquí està el fitxer:')
		->withAttachment($attachment);
	$bot->reply($message);
});

//$botman->hears('{test}', function($bot, $test) {
//	$bot->reply('has dit ' . $test);
//});

$botman->hears('User.*', function ($bot) {
	$user = $bot->getUser();
	$bot->reply('UserID = '. $user->getId());
});

$botman->hears('Event.*', function ($bot) {
	global $PAGE;
	$event = \block_xatbot\event\xatbot_viewed::create(array(
		'context' => $PAGE->context, 
	));
	$event->trigger();
	$user = $bot->getUser();
	$bot->reply('UserID = '. $user->getId());
});

$botman->hears('Prova', function($bot) {
	$bot->startConversation(new Prova\MyBotCommands);
});//'Prova\MyBotCommands@handle');




//Començament de les funcionalitats reals (AQUEST COMENTARI S'HAURÀ DE BORRAR)

$botman->hears('Recurs ([a-zA-Z ]*)', 'resource_listener::handle_resource_request');
//$botman->hears('Recurs ([a-zA-Z ]*)(|, course ([a-zA-Z ]*))(|, alumn ([a-zA-Z ]*))', 'Xatbot\resource_listener::handle_resource_request');

//function ($bot, $resourceName, $o1, $curs, $o2, $alumn){
//	$bot->reply('recurs: ' . $resourceName . ' course: ' . $curs . ' alumn: ' . $alumn);
//});

$botman->hears('call me {name}(| the {adjective})', function ($bot, $name, $o1, $adjective) {
    $bot->reply('Hello '.$name.'. You truly are '.$adjective);
});


$botman->fallback(function($bot) {
	$bot->reply('No entenc què dius');
});

// Start listening
$botman->listen();
