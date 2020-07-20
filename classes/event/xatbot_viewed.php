<?php

namespace block_xatbot\event;
defined('MOODLE_INTERNAL') || die();

class xatbot_viewed extends \core\event\base {

    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    public function get_description() {
        return 'User with id ' . $this->userid . ' has sent a message to chatbot ';
    }

    public static function get_name() {
        return get_string('eventtest', 'block_xatbot');
    }

    public function get_url() {
        return null;
    }

    public function get_context() {
        return $this->context;
    }

}