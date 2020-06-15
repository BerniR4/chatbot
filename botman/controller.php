<?php

require_once 'vendor/autoload.php';

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Messages\Attachments\File;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

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

//$botman->say('Message', 'my-recipient-user-id', WebDriver::class);
// Give the bot something to listen for.
$botman->hears('Hello', function ($bot) {
	$bot->reply('Bones! Sóc LSBot, un Xatbot que t\'ajudarà a cercar diferents recursos.');
	$bot->reply('Per cercar un recurs, utilitza la paraula clau "Recurs", seguit d\'allò que vulguis cercar. Per exemple: "Recurs prova"');
});
$botman->hears('Recurs .*', function ($bot) {
	$attachment = new File('../blocks/xatbot/block_xatbot.php', [
		'custom_payload' => true,
	]);
	$message = OutgoingMessage::create('Aquí està el fitxer:<a href="google.com">Exemple</a>')
		->withAttachment($attachment);
	$bot->reply($message);
});
global $PAGE;
$botman->hears('Curs .*', function ($bot) {
	$bot->reply('CourseID = ');
});

$botman->fallback(function($bot) {
	$bot->reply('No entenc què dius');
});

// Start listening
$botman->listen();
