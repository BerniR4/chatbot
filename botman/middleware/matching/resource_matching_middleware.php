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
 * Matching middleware class to check if the single petition request has matched.
 *
 * @package    block_chatbot
 * @copyright  2020 Daniel Amo, Bernat Rovirosa
 *  daniel.amo@salle.url.edu
 * @copyright  2020 La Salle Campus Barcelona, Universitat Ramon Llull https://www.salleurl.edu
 * @author     Daniel Amo
 * @author     Bernat Rovirosa
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Interfaces\Middleware\Matching;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class resource_matching_middleware implements Matching {

    public function matching(IncomingMessage $message, $pattern, $regexMatched) {

        preg_match('/' . $pattern . '/i', $message->getText(), $output);

        return $regexMatched && strlen($output['resname']) > 0 ;
    }
}