<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
//
// This file is part of CfM - Chatbot for Moodle
//
// CfM is a chatbot developed in Catalunya that helps search content in an easy,
// interactive and conversational manner. This project implements a chatbot inside a block
// for Moodle. Moodle is a Free Open source Learning Management System by Martin Dougiamas.
// CfM is a project initiated and leaded by Daniel Amo at the GRETEL research
// group at La Salle Campus Barcelona, Universitat Ramon Llull.
//
// CfM is copyrighted 2020 by Daniel Amo and Bernat Rovirosa
// of the La Salle Campus Barcelona, Universitat Ramon Llull https://www.salleurl.edu
// Contact info: Daniel Amo Filvà  danielamo @ gmail.com or daniel.amo @ salle.url.edu.

/**
 * BotMan controller file.
 *
 * @package    block_chatbot
 * @copyright  2020 Daniel Amo, Bernat Rovirosa
 *  daniel.amo@salle.url.edu
 * @copyright  2020 La Salle Campus Barcelona, Universitat Ramon Llull https://www.salleurl.edu
 * @author     Daniel Amo
 * @author     Bernat Rovirosa
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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
	$bot->reply(get_string('fullwelcome1', 'block_chatbot'));
	$bot->reply(get_string('fullwelcome2', 'block_chatbot'));
})->stopsConversation();

//Listen resource search single request
$botman->hears(get_string('hearingresourcerequest', 'block_chatbot'), 'resource_listener::handle_resource_request')
	->middleware(new resource_matching_middleware());

//Listen resource search conversation
$botman->hears(get_string('hearingresourceconver', 'block_chatbot'), function($bot) {
	$bot->startConversation(new resource_listener_conver);
});

$botman->fallback(function($bot) {
	//Create log
	global $PAGE;
	$context = context::instance_by_id($_GET['context']);
	if ($context->get_course_context(false) 
			&& (is_viewing(context::instance_by_id($_GET['context'])) 
			|| is_enrolled(context::instance_by_id($_GET['context'])))) {
		$event = \block_chatbot\event\fallback_executed::create(array(
			'context' => context::instance_by_id($_GET['context']), 
		));
	} else {
		$event = \block_chatbot\event\fallback_executed::create(array(
			'context' => $PAGE->context, 
		));
	}

	$event->trigger();

	$bot->reply('No entenc què dius');
});

// Start listening
$botman->listen();
