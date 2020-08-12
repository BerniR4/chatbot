<?php

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

    function handle_resource_request ($bot, $restype1, $restype2, $resname) {
		//$start = microtime(true);
		//$bot->reply(var_dump($bot));

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

		if ($rs_res != null && $rs_res->valid()) {
			//After create_messages, the result set is emptied, that's why it is needed assign a value to know it was
			//used (in order to send the correct message)
			$rs_res = self::create_messages($rs_res, TYPE_RESOURCE, $bot);		
		}

		if ($rs_url != null && $rs_url->valid()) {
			$rs_url = self::create_messages($rs_url, TYPE_URL, $bot);
		}
		
		if ($rs_asg != null && $rs_asg->valid()) {
			$rs_asg = self::create_messages($rs_asg, TYPE_ASSIGN, $bot);
		}

        if ($rs_res == null && $rs_url == null && $rs_asg == null) {
			$bot->reply(get_string('fullnoresourcematch', 'block_xatbot'));
		}

        //$time_elapsed_secs = microtime(true) - $start;
        //$bot->reply('Temps: ' . $time_elapsed_secs);

        global $PAGE;
        $context = context::instance_by_id($_GET['context'], IGNORE_MISSING);
		if ($context != false && $context->get_course_context(false) 
				&& (is_viewing($context) || is_enrolled($context))) {
            $event = \block_xatbot\event\resource_searched::create(array(
            'context' => $context, 
            ));
        } else {
            $event = \block_xatbot\event\resource_searched::create(array(
                'context' => $PAGE->context, 
            ));
        }

        $event->trigger();

    }

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
							$bot->reply(get_string('fullresourcematch', 'block_xatbot', get_string('pluginname', 'mod_resource')));
						}
						break;

					case TYPE_URL:
						$url = $record->externalurl;
						if ($aux == null) {
							$bot->reply(get_string('fullresourcematch', 'block_xatbot', get_string('pluginname', 'mod_url')));
						}
						break;

					case TYPE_ASSIGN: 
						$url = $CFG->wwwroot . '/mod/assign/view.php?id=' . $record->id;
						if ($aux == null) {
							$bot->reply(get_string('fullresourcematch', 'block_xatbot', get_string('pluginname', 'mod_assign')));
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
				$bot->reply(get_string('compresourcematchcourse', 'block_xatbot'));

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