<?php

namespace block_xatbot\event;
defined('MOODLE_INTERNAL') || die();

class fallback_executed extends \core\event\base {

    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    public function get_description() {
        return 'User with id ' . $this->userid . ' called an unknown chatbot function.';
    }

    public static function get_name() {
        return get_string('fallbackevent', 'block_xatbot');
    }

    public function get_url() {
        return null;
    }

    public function get_context() {
        return $this->context;
    }

}