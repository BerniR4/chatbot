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
// This file is part of XatBotMoodle
//
// XatBotMoodle is a chatbot developed in Catalunya that helps search content in an easy,
// interactive and conversational manner. This project implements a chatbot inside a block
// for Moodle. Moodle is a Free Open source Learning Management System by Martin Dougiamas.
// XatBotMoodle is a project initiated and leaded by Daniel Amo at the GRETEL research
// group at La Salle Campus Barcelona, Universitat Ramon Llull.
//
// XatBotMoodle is copyrighted 2020 by Daniel Amo and Bernat Rovirosa
// of the La Salle Campus Barcelona, Universitat Ramon Llull https://www.salleurl.edu
// Contact info: Daniel Amo Filvà  danielamo @ gmail.com or daniel.amo @ salle.url.edu.

/**
 * Strings for component 'block_chatbot', language 'ca'
 *
 * @package    block_chatbot
 * @copyright  2020 Daniel Amo, Bernat Rovirosa
 *  daniel.amo@salle.url.edu
 * @copyright  2020 La Salle Campus Barcelona, Universitat Ramon Llull https://www.salleurl.edu
 * @author     Daniel Amo
 * @author     Bernat Rovirosa
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Chatbot Block';
$string['chatbot'] = 'Chatbot';
$string['chatbot:addinstance'] = 'Add a new Chatbot Block';
$string['chatbot:myaddinstance'] = 'Add a new Chatbot Block to the My Moodle page';
$string['blockstring'] = 'Edit';

//Full static messages
$string['fullwelcome1'] = 'Hi! I\'m LSBot, a Chatbot who will help you retrieve information from Moodle.';
$string['fullwelcome2'] = 'To search for a resource, use the keyword "Resource", followed by what you want 
        to search for. For example: "Resource test"';
$string['fullfallback'] = 'Sorry, I don\'t understand what you said.';
$string['fullresourcematch'] = 'Of type "{$a}", the following matches have been found:';
$string['fullnoresourcematch'] = 'No matches have been found';
$string['fullaskresourcename'] = 'What is the name of the resource?';
$string['fullaskresourcetype'] = 'What type of resource do you want?';
$string['fullaskresourcecourse'] = 'Of which course?';

$string['buttonall'] = 'Tots';

//Static message components
$string['compresourcematchcourse'] = ' - Course: ';

//Hearing single requests
$string['hearingresourcerequest'] = '.*(Search(?<restype1> resource| file| url| assign)?|(Search )?(?<restype2>resource|file|url|assign)) (?<resname>.*)';

//Hearing conversations
$string['hearingresourceconver'] = '(Search|(?<restype2>resource|file|url|assign))';

//Events
$string['resourcesearchevent'] = 'Resource search';
$string['fallbackevent'] = 'Unknown request made';