<?php

require_once 'vendor/autoload.php';
require_once __DIR__ . '/../../../config.php' ;

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

include __DIR__ . '/functionalities/single_req/resource_listener.php';
include __DIR__ . '/functionalities/conversation/resource_listener_conver.php';
include __DIR__ . '/middleware/matching/resource_matching_middleware.php';

$config = [];

// Load the driver(s) you want to use
DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);

// Create an instance
$adapter = new FilesystemAdapter();
$botman = BotManFactory::create($config, new SymfonyCache($adapter));

//Welcome message 
$botman->hears('welcome_message', function ($bot) {
	$bot->reply(get_string('fullwelcome1', 'block_xatbot'));
	$bot->reply(get_string('fullwelcome2', 'block_xatbot'));
})->stopsConversation();

//$botman->hears('.*(Busca( recurs)?|(Busca )?recurs) (?<resname>.*)', 'resource_listener::handle_resource_request')
//	->middleware(new resource_matching_middleware());

$botman->hears(get_string('hearingresourcerequest', 'block_xatbot'), 'resource_listener::handle_resource_request')
	->middleware(new resource_matching_middleware());

$botman->hears(get_string('hearingresourceconver', 'block_xatbot'), function($bot) {
	$bot->startConversation(new resource_listener_conver);
});

$botman->fallback(function($bot) {
	$bot->reply('No entenc què dius');
});

// Start listening
$botman->listen();
