<?php

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

    function search_resource_files($bot, $resourcename) {
        global $DB, $CFG, $USER;
        $start = microtime(true);

        $rs = $DB->get_recordset_sql('SELECT r.id AS rid, r.name, c.id AS cid, r.revision, f.filename, cm.course, 
                cm.visible, course.fullname FROM {resource} AS r, {context} AS c, {course_modules} AS cm, {files} AS f,
                {course} AS course WHERE cm.deletioninprogress = 0 AND cm.module = :modulename 
                AND r.id = cm.instance AND c.instanceid = cm.id AND cm.course = course.id
                AND f.filename <> "." AND f.contextid = c.id AND c.contextlevel = :contextlevel 
                AND UPPER(r.name) LIKE CONCAT("%", UPPER(:resourcename), "%");',
            ['contextlevel' => CONTEXT_MODULE, 'resourcename' => $resourcename, 
            'modulename' => $DB->get_record('modules', ['name' => 'resource'])->id]);
        
        if (!$rs) {
            return false;
        }

        $bot->reply(get_string('fullresourcematch', 'block_xatbot', get_string('pluginname', 'mod_resource')));

        foreach ($rs as $record) {
            if (($record->visible 
                    || has_capability('moodle/course:viewhiddenactivities', context_course::instance($record->course))) 
                    && (is_enrolled(context_course::instance($record->course))) 
                    || (is_viewing(context_course::instance($record->course)))) {
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
        $time_elapsed_secs = microtime(true) - $start;
        $bot->reply('Temps: ' . $time_elapsed_secs);
        return true;
    }

    function search_resource_url($bot, $resourcename) {
        global $DB, $CFG, $USER, $PAGE;
        $rs = $DB->get_recordset_sql('SELECT u.name, u.externalurl, cm.course, cm.visible, course.fullname 
                FROM {url} AS u, {context} AS c, {course_modules} AS cm, {course} AS course 
                WHERE course.id = u.course AND c.instanceid = cm.id AND cm.module = 20 AND cm.deletioninprogress = 0 
                AND u.id = cm.instance AND c.contextlevel = :contextlevel
                AND UPPER(u.name) LIKE CONCAT("%", UPPER(:resourcename), "%");',
            ['contextlevel' => CONTEXT_MODULE, 'resourcename' => $resourcename]);
        
        if (!$rs) {
            return false;
        }

        $bot->reply(get_string('fullresourcematch', 'block_xatbot', get_string('pluginname', 'mod_url')));

        foreach ($rs as $record) {
            if (($record->visible 
                    || has_capability('moodle/course:viewhiddenactivities', context_course::instance($record->course))) 
                    && (is_enrolled(context_course::instance($record->course))) 
                    || (is_viewing(context_course::instance($record->course)))) {
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