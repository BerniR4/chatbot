<?php

//require_once __DIR__ . '/../../../../config.php';
//require_once 'vendor/autoload.php';

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Messages\Attachments\File;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use App\Http\Controllers\BotManController;

class resource_listener {
    function handle_resource_request ($bot, $resourcename) {
        global $DB, $CFG, $USER;
        $rs = $DB->get_records_sql('SELECT r.id AS rid, r.name, c.id AS cid, r.revision, f.filename, cm.course, 
                cm.visible FROM {resource} AS r, {context} AS c, {course_modules} AS cm, {files} AS f 
                WHERE cm.deletioninprogress = 0 AND cm.module = 17 AND r.id = cm.instance AND c.instanceid = cm.id 
                AND f.filename <> "." AND f.contextid = c.id AND c.contextlevel = :contextlevel 
                AND UPPER(r.name) LIKE CONCAT("%", UPPER(:resourcename), "%");',
            ['contextlevel' => CONTEXT_MODULE, 'resourcename' => $resourcename]);
        
        if (!$rs) {
            $bot->reply('No s\'han trobat coincidencies');
            return;
        }

        $bot->reply('S\'han trobat les següents coincidencies:');

        foreach ($rs as $record) {
            if (($record->visible || has_capability('moodle/course:viewhiddenactivities', 
                    context_course::instance($record->course))) 
                    && is_enrolled(context_course::instance($record->course), $USER->id)) {
                $attachment = new File($CFG->wwwroot . '/pluginfile.php/' . $record->cid . '/mod_resource/content/' 
                    . $record->revision . '/' . $record->filename, [
                    'custom_payload' => true,
                ]);
                $message = OutgoingMessage::create($record->name)
                    ->withAttachment($attachment);
                $bot->reply($message);
            }
        }

    }
}