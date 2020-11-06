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
 * Search resource functionality, conversation mode.
 *
 * @package    block_chatbot
 * @copyright  2020 Daniel Amo, Bernat Rovirosa
 *  daniel.amo@salle.url.edu
 * @copyright  2020 La Salle Campus Barcelona, Universitat Ramon Llull https://www.salleurl.edu
 * @author     Daniel Amo
 * @author     Bernat Rovirosa
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Attachments\File;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

include_once __DIR__ . '/../dbhelpers/resource_dbhelper.php';

class resource_listener_conver extends Conversation {

	/** @var string */
	protected $name;

	/** @var string */
	protected $type;

	/** @var string */
	protected $course;

	/**
	 * Asks the name of the resource.
	 */
	public function ask_name() {
		$question = Question::create(get_string('fullaskresourcename', 'block_chatbot'))
        ->addButtons([
			Button::create(get_string('buttonall', 'block_chatbot'))->value(get_string('buttonall', 'block_chatbot'))
		]);

		$this->ask($question, function (Answer $answer) {
			$this->name = null;
			if (strcasecmp($answer->getText(), get_string('buttonall', 'block_chatbot')) != 0) {
				$this->name = $answer->getText(); 
			}

			if ($this->type == null) {
				$this->ask_type();
			} else {
				$this->ask_course();
			}
		});
	}

	/**
	 * Asks the type of the resource (file, url or assign).
	 */
	public function ask_type() {
		
		$question = Question::create(get_string('fullaskresourcetype', 'block_chatbot'))
        ->addButtons([
            Button::create(get_string('pluginname', 'mod_resource'))->value(get_string('pluginname', 'mod_resource')),
			Button::create(get_string('pluginname', 'mod_url'))->value(get_string('pluginname', 'mod_url')),
			Button::create(get_string('pluginname', 'mod_assign'))->value(get_string('pluginname', 'mod_assign')),
			Button::create(get_string('buttonall', 'block_chatbot'))->value(get_string('buttonall', 'block_chatbot'))
        ]);

		$this->ask($question, function (Answer $answer) {
			$this->type = null;
			if (strcasecmp($answer->getText(), get_string('pluginname', 'mod_resource')) == 0) {
				$this->type = TYPE_RESOURCE;
			} elseif (strcasecmp($answer->getText(), get_string('pluginname', 'mod_url')) == 0) {
				$this->type = TYPE_URL;
			} elseif (strcasecmp($answer->getText(), get_string('pluginname', 'mod_assign')) == 0) {
				$this->type = TYPE_ASSIGN;
			}

			$this->ask_course();
		});
	}

	/**
	 * Asks in which course the resource is to be searched
	 */
	public function ask_course() {
		$question = Question::create(get_string('fullaskresourcecourse', 'block_chatbot'))
        ->addButtons([
			Button::create(get_string('buttonall', 'block_chatbot'))->value(get_string('buttonall', 'block_chatbot'))
		]);
		
		$this->ask($question, function (Answer $answer) {
			$this->course = null;
			if (strcasecmp($answer->getText(), get_string('buttonall', 'block_chatbot')) != 0) {
				$this->course = $answer->getText(); 
			}
			$this->manage_reply();
		});
	}

	/**
	 * Using the parameters, this function decides what it needs to be searched and manages
	 * the result of this search.
	 */
	public function manage_reply() {
		$rs_res = null;
		$rs_url = null;
		$rs_asg = null;

		$res_b = false;
		$url_b = false;
		$asg_b = false;

		//Searches the resources depending on the parameters specified
		switch ($this->type) {
			case TYPE_RESOURCE:
				$rs_res = resource_dbhelper::search_resource_files($this->name, $this->course);
				break;
			
			case TYPE_URL:
				$rs_url = resource_dbhelper::search_resource_url($this->name, $this->course);
				break;

			case TYPE_ASSIGN:
				$rs_asg = resource_dbhelper::search_resource_assign($this->name, $this->course);
				break;

			default:
				$rs_res = resource_dbhelper::search_resource_files($this->name, $this->course);
				$rs_url = resource_dbhelper::search_resource_url($this->name, $this->course);
				$rs_asg = resource_dbhelper::search_resource_assign($this->name, $this->course);

		}

		//Checks the results of the search
		if ($rs_res != null && $rs_res->valid()) {
			//After create_messages, the result set is emptied, that's why it is needed assign a value to know it was
			//used (in order to send the correct message)
			$res_b = $this->create_messages($rs_res, TYPE_RESOURCE);		
		}

		if ($rs_url != null && $rs_url->valid()) {
			$url_b = $this->create_messages($rs_url, TYPE_URL);
		}
		
		if ($rs_asg != null && $rs_asg->valid()) {
			$asg_b = $this->create_messages($rs_asg, TYPE_ASSIGN);
		}
		
		if ($res_b == null && $url_b == null && $asg_b == null) {
			$this->say(get_string('fullnoresourcematch', 'block_chatbot'));
		}

		//Creates log
        global $PAGE;
        $context = context::instance_by_id($_GET['context']);
        if ($context->get_course_context(false) 
                && (is_viewing(context::instance_by_id($_GET['context'])) 
                || is_enrolled(context::instance_by_id($_GET['context'])))) {
            $event = \block_chatbot\event\resource_searched::create(array(
                'context' => context::instance_by_id($_GET['context']), 
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
	 */
	public function create_messages($rs, $type) {
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
							$this->say(get_string('fullresourcematch', 'block_chatbot', get_string('pluginname', 'mod_resource')));
						}
						break;

					case TYPE_URL:
						$url = $CFG->wwwroot . '/mod/url/view.php?id=' . $record->id;
						if ($aux == null) {
							$this->say(get_string('fullresourcematch', 'block_chatbot', get_string('pluginname', 'mod_url')));
						}
						break;

					case TYPE_ASSIGN: 
						$url = $CFG->wwwroot . '/mod/assign/view.php?id=' . $record->id;
						if ($aux == null) {
							$this->say(get_string('fullresourcematch', 'block_chatbot', get_string('pluginname', 'mod_assign')));
						}
						break;

				}
				$aux = true;

				$attachment = new File($url, [
					'custom_payload' => true,
				]);
                $message = OutgoingMessage::create($record->name)
                    ->withAttachment($attachment);
				$this->say($message);
				
				//Send ' - course: ' separator
				$this->say(get_string('compresourcematchcourse', 'block_chatbot'));

				//Send Course name with link
				$attachment = new File($CFG->wwwroot . '/course/view.php?id=' . $record->course, [
                    'custom_payload' => true,
                ]);
                $message = OutgoingMessage::create($record->fullname)
                    ->withAttachment($attachment);
                $this->say($message);
            }
		}
		$rs->close();
		return $aux;
	}

	public function run() {
		$this->type = null;
		$restype1 = $this->getBot()->getMatches()['restype1'];
		$restype2 = $this->getBot()->getMatches()['restype2'];
		$aux = null;
		if ($restype1 != null) {
			$aux = trim($restype1);
		} elseif ($restype2 != null) {
			$aux = trim($restype2);
		}

		if (strcasecmp($aux, get_string('pluginname', 'mod_resource')) == 0) {
			$this->type = TYPE_RESOURCE;
		} elseif (strcasecmp($aux, get_string('pluginname', 'mod_url')) == 0) {
			$this->type = TYPE_URL;
		} elseif (strcasecmp($aux, get_string('pluginname', 'mod_assign')) == 0) {
			$this->type = TYPE_ASSIGN;
		}

		$this->ask_name();
	}

}
