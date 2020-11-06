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
 * Database helper. It manages the requests to search for resources
 *
 * @package    block_chatbot
 * @copyright  2020 Daniel Amo, Bernat Rovirosa
 *  daniel.amo@salle.url.edu
 * @copyright  2020 La Salle Campus Barcelona, Universitat Ramon Llull https://www.salleurl.edu
 * @author     Daniel Amo
 * @author     Bernat Rovirosa
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('TYPE_RESOURCE') || define('TYPE_RESOURCE', 1);
defined('TYPE_URL') || define('TYPE_URL', 2);
defined('TYPE_ASSIGN') || define('TYPE_ASSIGN', 3);

class resource_dbhelper {

    /**
     * This function queries file resources to the database
     * 
     * @param string    $resourcename   name of the file
     * @param string    $coursename     name of the course
     * @param integer   $courseid       identifier of the course 
     */
    public static function search_resource_files($resourcename, $coursename, $courseid = null) {
        global $DB, $USER;

        $select = 'SELECT DISTINCT r.id AS rid, r.name, c.id AS cid, r.revision, f.filename, cm.course, cm.visible, 
            course.fullname';
        $from = ' FROM {resource} AS r, {context} AS c, {course_modules} AS cm, {files} AS f,
            {course} AS course';
        $where = ' WHERE cm.deletioninprogress = 0 AND cm.module = :moduleid AND r.id = cm.instance 
            AND c.instanceid = cm.id AND cm.course = course.id
            AND f.filename <> "." AND f.contextid = c.id AND c.contextlevel = :contextlevel';

        if (!is_siteadmin()) {
            //Maybe it should be checked if it is a course (c2.module = 50)
            $from = $from . ', {role_assignments} AS ra, {context} AS c2';
            $where = $where . ' AND c2.id = ra.contextid AND ra.userid = :userid AND c2.instanceid = cm.course';
        }

        if ($resourcename != null) {
            $where = $where . ' AND UPPER(r.name) LIKE CONCAT("%", UPPER(:resourcename), "%")';
        }

        if ($coursename != null) {
            $where = $where . ' AND UPPER(course.fullname) LIKE CONCAT("%", UPPER(:coursename), "%")';
        }

        if ($courseid != null) {
            $where = $where . ' AND course.id = :courseid';
        }

        $query = $select . $from . $where . ';';

        $rs = $DB->get_recordset_sql($query, ['contextlevel' => CONTEXT_MODULE, 'resourcename' => $resourcename, 
            'moduleid' => $DB->get_record('modules', ['name' => 'resource'])->id, 'coursename' => $coursename,
            'userid' => $USER->id, 'courseid' => $courseid]);
        
        return $rs;
    }

    /**
     * This function queries url resources to the database
     * 
     * @param string    $resourcename   name of the file
     * @param string    $coursename     name of the course
     * @param integer   $courseid       identifier of the course 
     */    
    public static function search_resource_url($resourcename, $coursename, $courseid = null) {
        global $DB, $USER;

        $select = 'SELECT DISTINCT u.name, cm.id, cm.course, cm.visible, course.fullname';
        $from = ' FROM {url} AS u, {context} AS c, {course_modules} AS cm, {course} AS course';
        $where = ' WHERE course.id = cm.course AND c.instanceid = cm.id AND cm.module = :moduleid 
            AND cm.deletioninprogress = 0 AND u.id = cm.instance AND c.contextlevel = :contextlevel';

        if (!is_siteadmin()) {
            //Maybe it should be checked if it is a course (c2.module = 50)
            $from = $from . ', {role_assignments} AS ra, {context} AS c2';
            $where = $where . ' AND c2.id = ra.contextid AND ra.userid = :userid AND c2.instanceid = cm.course';
        }
        
        if ($resourcename != null) {
            $where = $where . ' AND UPPER(u.name) LIKE CONCAT("%", UPPER(:resourcename), "%")';
        }

        if ($coursename != null) {
            $where = $where . ' AND UPPER(course.fullname) LIKE CONCAT("%", UPPER(:coursename), "%")';
        }

        if ($courseid != null) {
            $where = $where . ' AND course.id = :courseid';
        }

        $query = $select . $from . $where . ';';

        $rs = $DB->get_recordset_sql($query, ['contextlevel' => CONTEXT_MODULE, 'resourcename' => $resourcename, 
            'moduleid' => $DB->get_record('modules', ['name' => 'url'])->id, 'coursename' => $coursename,
            'userid' => $USER->id, 'courseid' => $courseid]);

        return $rs;
    }

    /**
     * This function queries assign resources to the database
     * 
     * @param string    $resourcename   name of the file
     * @param string    $coursename     name of the course
     * @param integer   $courseid       identifier of the course 
     */
    public static function search_resource_assign($resourcename, $coursename, $courseid = null) {
        global $DB, $USER;

        $select = 'SELECT DISTINCT a.name, cm.id, cm.course, cm.visible, course.fullname ';
        $from = ' FROM {assign} AS a, {context} AS c, {course_modules} AS cm, {course} AS course';
        $where = ' WHERE course.id = cm.course AND c.instanceid = cm.id AND cm.module = :moduleid 
            AND cm.deletioninprogress = 0 AND a.id = cm.instance AND c.contextlevel = :contextlevel';

        if (!is_siteadmin()) {
            //Maybe it should be checked if it is a course (c2.module = 50)
            $from = $from . ', {role_assignments} AS ra, {context} AS c2';
            $where = $where . ' AND c2.id = ra.contextid AND ra.userid = :userid AND c2.instanceid = cm.course';
        }

        if ($resourcename != null) {
            $where = $where . ' AND UPPER(a.name) LIKE CONCAT("%", UPPER(:resourcename), "%")';
        }

        if ($coursename != null) {
            $where = $where . ' AND UPPER(course.fullname) LIKE CONCAT("%", UPPER(:coursename), "%")';
        }

        if ($courseid != null) {
            $where = $where . ' AND course.id = :courseid';
        }

        $query = $select . $from . $where . ';';

        $rs = $DB->get_recordset_sql($query, ['contextlevel' => CONTEXT_MODULE, 'resourcename' => $resourcename, 
            'moduleid' => $DB->get_record('modules', ['name' => 'assign'])->id, 'coursename' => $coursename,
            'userid' => $USER->id, 'courseid' => $courseid]);
        
        return $rs;
    }

}