<?php

namespace block_xatbot\event;
defined('MOODLE_INTERNAL') || die();

class resource_searched extends \core\event\base {

    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    public function get_description() {
        return 'User with id ' . $this->userid . ' has searched a resource using the chatbot.';
    }

    public static function get_name() {
        return get_string('resourcesearchevent', 'block_xatbot');
    }

    public function get_url() {
        return null;
    }

    public function get_context() {
        return $this->context;
    }

}