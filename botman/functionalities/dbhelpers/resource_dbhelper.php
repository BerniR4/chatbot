<?php

class resource_dbhelper {

    public static function search_resource_files($resourcename, $course) {
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

        if ($course != null) {
            $where = $where . ' AND UPPER(course.fullname) LIKE CONCAT("%", UPPER(:coursename), "%")';
        }

        $query = $select . $from . $where . ';';

        $rs = $DB->get_recordset_sql($query, ['contextlevel' => CONTEXT_MODULE, 'resourcename' => $resourcename, 
            'moduleid' => $DB->get_record('modules', ['name' => 'resource'])->id, 'coursename' => $course,
            'userid' => $USER->id]);
        
        return $rs;
    }

    public static function search_resource_url($resourcename, $course) {
        global $DB, $USER;

        $select = 'SELECT DISTINCT u.name, u.externalurl, cm.course, cm.visible, course.fullname';
        $from = ' FROM {url} AS u, {context} AS c, {course_modules} AS cm, {course} AS course';
        $where = ' WHERE course.id = u.course AND c.instanceid = cm.id AND cm.module = :moduleid 
            AND cm.deletioninprogress = 0 AND u.id = cm.instance AND c.contextlevel = :contextlevel';

        if (!is_siteadmin()) {
            //Maybe it should be checked if it is a course (c2.module = 50)
            $from = $from . ', {role_assignments} AS ra, {context} AS c2';
            $where = $where . ' AND c2.id = ra.contextid AND ra.userid = :userid AND c2.instanceid = cm.course';
        }
        
        if ($resourcename != null) {
            $where = $where . ' AND UPPER(u.name) LIKE CONCAT("%", UPPER(:resourcename), "%")';
        }

        if ($course != null) {
            $where = $where . ' AND UPPER(course.fullname) LIKE CONCAT("%", UPPER(:coursename), "%")';
        }

        $query = $select . $from . $where . ';';

        $rs = $DB->get_recordset_sql($query, ['contextlevel' => CONTEXT_MODULE, 'resourcename' => $resourcename, 
            'moduleid' => $DB->get_record('modules', ['name' => 'url'])->id, 'coursename' => $course,
            'userid' => $USER->id]);

        return $rs;
    }

    public static function search_resource_assign($resourcename, $course) {
        global $DB, $USER;

        $select = 'SELECT DISTINCT a.name, cm.id, cm.course, cm.visible, course.fullname ';
        $from = ' FROM {assign} AS a, {context} AS c, {course_modules} AS cm, {course} AS course';
        $where = ' WHERE course.id = a.course AND c.instanceid = cm.id AND cm.module = :moduleid 
            AND cm.deletioninprogress = 0 AND a.id = cm.instance AND c.contextlevel = :contextlevel';

        if (!is_siteadmin()) {
            //Maybe it should be checked if it is a course (c2.module = 50)
            $from = $from . ', {role_assignments} AS ra, {context} AS c2';
            $where = $where . ' AND c2.id = ra.contextid AND ra.userid = :userid AND c2.instanceid = cm.course';
        }

        if ($resourcename != null) {
            $where = $where . ' AND UPPER(a.name) LIKE CONCAT("%", UPPER(:resourcename), "%")';
        }

        if ($course != null) {
            $where = $where . ' AND UPPER(course.fullname) LIKE CONCAT("%", UPPER(:coursename), "%")';
        }

        $query = $select . $from . $where . ';';

        $rs = $DB->get_recordset_sql($query, ['contextlevel' => CONTEXT_MODULE, 'resourcename' => $resourcename, 
            'moduleid' => $DB->get_record('modules', ['name' => 'assign'])->id, 'coursename' => $course,
            'userid' => $USER->id]);
        
        return $rs;
    }

}