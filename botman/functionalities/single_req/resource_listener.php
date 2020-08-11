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

    //may be a little redundant, as they are also defined in the resource_listener_conver.
    const TYPE_RESOURCE = 1;
	const TYPE_URL = 2;
	const TYPE_ASSIGN = 3;

    function handle_resource_request ($bot, $resourcename) {
        //$start = microtime(true);

        $rs_res = resource_dbhelper::search_resource_files($resourcename, null);
        $rs_url = resource_dbhelper::search_resource_url($resourcename, null);
        $rs_asg = resource_dbhelper::search_resource_assign($resourcename, null);

        $rs_res = self::create_messages($rs_res, self::TYPE_RESOURCE, $bot);		
        $rs_url = self::create_messages($rs_url, self::TYPE_URL, $bot);
        $rs_asg = self::create_messages($rs_asg, self::TYPE_ASSIGN, $bot);

        if ($rs_res == null && $rs_url == null && $rs_asg == null) {
			$bot->reply(get_string('fullnoresourcematch', 'block_xatbot'));
		}

        //$time_elapsed_secs = microtime(true) - $start;
        //$bot->reply('Temps: ' . $time_elapsed_secs);

        global $PAGE;
        $context = context::instance_by_id($_GET['context']);
        if ($context->get_course_context(false) 
                && (is_viewing(context::instance_by_id($_GET['context'])) 
                || is_enrolled(context::instance_by_id($_GET['context'])))) {
            $event = \block_xatbot\event\resource_searched::create(array(
            'context' => context::instance_by_id($_GET['context']), 
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
					case self::TYPE_RESOURCE: 
						$url = $CFG->wwwroot . '/pluginfile.php/' . $record->cid . '/mod_resource/content/' 
							. $record->revision . '/' . $record->filename;
						if ($aux == null) {
							$bot->reply(get_string('fullresourcematch', 'block_xatbot', get_string('pluginname', 'mod_resource')));
						}
						break;

					case self::TYPE_URL:
						$url = $record->externalurl;
						if ($aux == null) {
							$bot->reply(get_string('fullresourcematch', 'block_xatbot', get_string('pluginname', 'mod_url')));
						}
						break;

					case self::TYPE_ASSIGN: 
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