<?php

class resource_dbhelper {

    public static function search_resource_files($resourcename, $course) {
        global $DB;

        $moduleid = $DB->get_record('modules', ['name' => 'resource'])->id;

        $select = 'SELECT r.id AS rid, r.name, c.id AS cid, r.revision, f.filename, cm.course, cm.visible, course.fullname ';
        $from = 'FROM {resource} AS r, {context} AS c, {course_modules} AS cm, {files} AS f,
            {course} AS course ';
        $where = 'WHERE cm.deletioninprogress = 0 AND cm.module = ' . $moduleid . ' AND r.id = cm.instance 
            AND c.instanceid = cm.id AND cm.course = course.id
            AND f.filename <> "." AND f.contextid = c.id AND c.contextlevel = ' . CONTEXT_MODULE;

        if ($resourcename != null) {
            $where = $where . ' AND UPPER(r.name) LIKE CONCAT("%", UPPER("' . $resourcename . '"), "%")';
        }

        if ($course != null) {
            $where = $where . ' AND UPPER(course.fullname) LIKE CONCAT("%", UPPER("' . $course . '"), "%")';
        }

        $where = $where . ';';

        $rs = $DB->get_records_sql($select . $from . $where);
        
        return $rs;
    }

    public static function search_resource_url($resourcename, $course) {
        global $DB;

        $moduleid = $DB->get_record('modules', ['name' => 'url'])->id;

        $select = 'SELECT u.name, u.externalurl, cm.course, cm.visible, course.fullname ';
        $from = 'FROM {url} AS u, {context} AS c, {course_modules} AS cm, {course} AS course ';
        $where = 'WHERE course.id = u.course AND c.instanceid = cm.id AND cm.module = ' . $moduleid
            . ' AND cm.deletioninprogress = 0 AND u.id = cm.instance AND c.contextlevel = ' . CONTEXT_MODULE;

        if ($resourcename != null) {
            $where = $where . ' AND UPPER(u.name) LIKE CONCAT("%", UPPER("' . $resourcename . '"), "%")';
        }

        if ($course != null) {
            $where = $where . ' AND UPPER(course.fullname) LIKE CONCAT("%", UPPER("' . $course . '"), "%")';
        }

        $where = $where . ';';

        $rs = $DB->get_records_sql($select . $from . $where);
        
        return $rs;
    }
}