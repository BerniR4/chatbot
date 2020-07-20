<?php

defined('MOODLE_INTERNAL') || die();

$observers = array(
    array(
        'eventname'     =>  '\block_xatbot\event\xatbot_viewed',
        'callback'      =>  'test_event::botman_test',
    ),
);