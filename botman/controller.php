<?php

require_once 'vendor/autoload.php';

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;

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
	$bot->reply('Hello yourself');
});
$botman->hears('.*Hello.*', function ($bot) {
	$bot->reply('Hello!');
});
/*$botman->hears('{text}', function ($bot, $text) {
	$bot->reply('Your First Response' .$text . $request);
});*/
/*
$botman->fallback(function($bot) {
	$bot->reply('No entenc què dius');
});
*/
// Start listening
$botman->listen();
