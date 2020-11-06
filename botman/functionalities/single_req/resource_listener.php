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
 * Search resource functionality, single request mode.
 *
 * @package    block_chatbot
 * @copyright  2020 Daniel Amo, Bernat Rovirosa
 *  daniel.amo@salle.url.edu
 * @copyright  2020 La Salle Campus Barcelona, Universitat Ramon Llull https://www.salleurl.edu
 * @author     Daniel Amo
 * @author     Bernat Rovirosa
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Messages\Attachments\File;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use App\Http\Controllers\BotManController;

include_once __DIR__ . '/../dbhelpers/resource_dbhelper.php';

class resource_listener {

	/**
	 * Using the parameters, this function decides what it needs to be searched and manages
	 * the result of this search.
	 */
    function handle_resource_request ($bot, $restype1, $restype2, $resname) {
		$restype = null;

		if ($restype1 != null) {
			$restype = trim($restype1);
		} elseif ($restype2 != null) {
			$restype = trim($restype2);
		}

		if (strcasecmp($restype, get_string('pluginname', 'mod_resource')) == 0) {
			$restype = TYPE_RESOURCE;
		} elseif (strcasecmp($restype, get_string('pluginname', 'mod_url')) == 0) {
			$restype = TYPE_URL;
		} elseif (strcasecmp($restype, get_string('pluginname', 'mod_assign')) == 0) {
			$restype = TYPE_ASSIGN;
		}

		$courseid = null;
		$context = context_course::instance($_GET['course'], IGNORE_MISSING);
		if ($_GET['course'] != 1 && $context != false && $context->get_course_context(false) 
				&& (is_viewing($context) || is_enrolled($context))) {
			$courseid = $_GET['course'];
		}

		//Searches the resources depending on the parameters specified
		switch ($restype) {
			case TYPE_RESOURCE:
				$rs_res = resource_dbhelper::search_resource_files($resname, null, $courseid);
				break;
			
			case TYPE_URL:
				$rs_url = resource_dbhelper::search_resource_url($resname, null, $courseid);
				break;

			case TYPE_ASSIGN:
				$rs_asg = resource_dbhelper::search_resource_assign($resname, null, $courseid);
				break;

			default:
				$rs_res = resource_dbhelper::search_resource_files($resname, null, $courseid);
				$rs_url = resource_dbhelper::search_resource_url($resname, null, $courseid);
				$rs_asg = resource_dbhelper::search_resource_assign($resname, null, $courseid);

		}
		
		$res_b = false;
		$url_b = false;
		$asg_b = false;

		//Checks the results of the search
		if ($rs_res != null && $rs_res->valid()) {
			//After create_messages, the result set is emptied, that's why it is needed assign a value to know it was
			//used (in order to send the correct message)
			$res_b = self::create_messages($rs_res, TYPE_RESOURCE, $bot);		
		}

		if ($rs_url != null && $rs_url->valid()) {
			$url_b = self::create_messages($rs_url, TYPE_URL, $bot);
		}
		
		if ($rs_asg != null && $rs_asg->valid()) {
			$asg_b = self::create_messages($rs_asg, TYPE_ASSIGN, $bot);
		}

		if ($res_b == null && $url_b == null && $asg_b == null) {
			$bot->reply(get_string('fullnoresourcematch', 'block_chatbot'));
		}

		//Creates log
        global $PAGE;
        $context = context::instance_by_id($_GET['context'], IGNORE_MISSING);
		if ($context != false && $context->get_course_context(false) 
				&& (is_viewing($context) || is_enrolled($context))) {
            $event = \block_chatbot\event\resource_searched::create(array(
            'context' => $context, 
            ));
        } else {
            $event = \block_chatbot\event\resource_searched::create(array(
                'context' => $PAGE->context, 
            ));
        }

        $event->trigger();

    }

	/**
	 * Creates the message and sends it to the user.
	 * 
	 * @param Object	$rs		result of the search
	 * @param integer	$type	type of the result (file, url or assign)
	 * @param BotMan	$bot	the botman class used to send the messages
	 */
    public function create_messages($rs, $type, $bot) {
		global $CFG, $USER;
		$aux = null;
		foreach ($rs as $record) {
            if ($record->visible 
                    || has_capability('moodle/course:viewhiddenactivities', context_course::instance($record->course))) {
				
				//Send Resource name with link
				$url = '';
				switch ($type) {
					case TYPE_RESOURCE: 
						$url = $CFG->wwwroot . '/pluginfile.php/' . $record->cid . '/mod_resource/content/' 
							. $record->revision . '/' . $record->filename;
						if ($aux == null) {
							$bot->reply(get_string('fullresourcematch', 'block_chatbot', get_string('pluginname', 'mod_resource')));
						}
						break;

					case TYPE_URL:
						$url = $CFG->wwwroot . '/mod/url/view.php?id=' . $record->id;
						if ($aux == null) {
							$bot->reply(get_string('fullresourcematch', 'block_chatbot', get_string('pluginname', 'mod_url')));
						}
						break;

					case TYPE_ASSIGN: 
						$url = $CFG->wwwroot . '/mod/assign/view.php?id=' . $record->id;
						if ($aux == null) {
							$bot->reply(get_string('fullresourcematch', 'block_chatbot', get_string('pluginname', 'mod_assign')));
						}
						break;

				}
				$aux = true;

				$attachment = new File($url, [
					'custom_payload' => true,
				]);
                $message = OutgoingMessage::create($record->name)
                    ->withAttachment($attachment);
				$bot->reply($message);
				
				//Send ' - course: ' separator
				$bot->reply(get_string('compresourcematchcourse', 'block_chatbot'));

				//Send Course name with link
				$attachment = new File($CFG->wwwroot . '/course/view.php?id=' . $record->course, [
                    'custom_payload' => true,
                ]);
                $message = OutgoingMessage::create($record->fullname)
                    ->withAttachment($attachment);
                $bot->reply($message);
            }
		}
		$rs->close();
		return $aux;
	}
}