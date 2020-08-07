<?php

require_once 'vendor/autoload.php';

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Messages\Attachments\File;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use App\Http\Controllers\BotManController;
use BotMan\BotMan\Cache\SymfonyCache;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use BotMan\BotMan\Commands\Command;

include 'prova.php';
include __DIR__ . '/functionalities/single_req/resource_listener.php';
include __DIR__ . '/functionalities/conversation/resource_listener_conver.php';
include __DIR__ . '/middleware/matching/resource_matching_middleware.php';

$config = [];

// Load the driver(s) you want to use
DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);

// Create an instance
$adapter = new FilesystemAdapter();
$botman = BotManFactory::create($config, new SymfonyCache($adapter));
//$botman = BotManFactory::create($config);

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

$botman->hears('call me ([^\s]+)( the ([^\s]+))?( with ([^\s]+) size)?', function ($bot, $name, $adjective, $friki) {
    $bot->reply('Hello '.$name.'. You truly are '.$adjective);
});

$botman->hears('Prova2 {nametest}', function($bot, $nametest) {
	$bot->reply('Hello You truly are' . var_dump($bot->getMessage()));
})->middleware(new resource_matching_middleware());

////////////////////////////////////////////////////////////////////////////////

//Welcome message 
$botman->hears('Hello', function ($bot) {
	$bot->reply(get_string('fullwelcome1', 'block_xatbot'));
	$bot->reply(get_string('fullwelcome2', 'block_xatbot'));
})->stopsConversation();

$botman->hears(get_string('hearingresourcerequest', 'block_xatbot'), 'resource_listener::handle_resource_request');
//$botman->hears('Recurs ([a-zA-Z ]*)(|, course ([a-zA-Z ]*))(|, alumn ([a-zA-Z ]*))', 'Xatbot\resource_listener::handle_resource_request');

$botman->hears(get_string('hearingresourceconver', 'block_xatbot'), function($bot) {
	$bot->startConversation(new resource_listener_conver);
});

//function ($bot, $resourceName, $o1, $curs, $o2, $alumn){
//	$bot->reply('recurs: ' . $resourceName . ' course: ' . $curs . ' alumn: ' . $alumn);
//});

$botman->fallback(function($bot) {
	$bot->reply('No entenc què dius');
});

// Start listening
$botman->listen();
