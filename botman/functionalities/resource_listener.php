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
        $file_search = self::search_resource_files($bot, $resourcename);
        $url_search = self::search_resource_url($bot, $resourcename);

        if (!$file_search && !$url_search) {
            $bot->reply(get_string('fullnoresourcematch', 'block_xatbot'));
        }
    }

    function search_resource_files($bot, $resourcename) {
        global $DB, $CFG, $USER;
        $rs = $DB->get_records_sql('SELECT r.id AS rid, r.name, c.id AS cid, r.revision, f.filename, cm.course, 
                cm.visible, course.fullname FROM {resource} AS r, {context} AS c, {course_modules} AS cm, {files} AS f,
                {course} AS course WHERE cm.deletioninprogress = 0 AND cm.module = 17 AND r.id = cm.instance 
                AND c.instanceid = cm.id AND cm.course = course.id
                AND f.filename <> "." AND f.contextid = c.id AND c.contextlevel = :contextlevel 
                AND UPPER(r.name) LIKE CONCAT("%", UPPER(:resourcename), "%");',
            ['contextlevel' => CONTEXT_MODULE, 'resourcename' => $resourcename]);
        
        if (!$rs) {
            return false;
        }

        $bot->reply(get_string('fullresourcematch', 'block_xatbot', get_string('pluginname', 'mod_resource')));

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
                $bot->reply(get_string('compresourcematchcourse', 'block_xatbot'));

                $attachment = new File($CFG->wwwroot . '/course/view.php?id=' . $record->course, [
                    'custom_payload' => true,
                ]);
                $message = OutgoingMessage::create($record->fullname)
                    ->withAttachment($attachment);
                $bot->reply($message);
            }
        }
        return true;
    }

    function search_resource_url($bot, $resourcename) {
        global $DB, $CFG, $USER;
        $rs = $DB->get_records_sql('SELECT u.name, u.externalurl, c.id AS course, c.fullname FROM {url} AS u, 
                {course} AS c WHERE c.id = u.course AND UPPER(u.name) LIKE CONCAT("%", UPPER(:resourcename), "%");',
            ['resourcename' => $resourcename]);
        
        if (!$rs) {
            return false;
        }

        $bot->reply(get_string('fullresourcematch', 'block_xatbot', get_string('pluginname', 'mod_url')));

        foreach ($rs as $record) {
            if (has_capability('moodle/course:viewhiddenactivities', context_course::instance($record->course)) 
                    && is_enrolled(context_course::instance($record->course), $USER->id)) {
                $attachment = new File($record->externalurl, [
                    'custom_payload' => true,
                ]);
                $message = OutgoingMessage::create($record->name)
                    ->withAttachment($attachment);
                $bot->reply($message);
                $bot->reply(get_string('compresourcematchcourse', 'block_xatbot'));

                $attachment = new File($CFG->wwwroot . '/course/view.php?id=' . $record->course, [
                    'custom_payload' => true,
                ]);
                $message = OutgoingMessage::create($record->fullname)
                    ->withAttachment($attachment);
                $bot->reply($message);
            }
        }
        return true;
    }
}