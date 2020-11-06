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

$string['pluginname'] = 'Bloc Xatbot';
$string['chatbot'] = 'Xatbot';
$string['chatbot:addinstance'] = 'Afegeix un nou Bloc Xatbot';
$string['chatbot:myaddinstance'] = 'Afegeix un nou Bloc Xatbot a la meva pagina de Moodle';
$string['blockstring'] = 'Modifica el text';

//Missatges estàtics complets
$string['fullwelcome1'] = 'Bones! Sóc un Xatbot que t\'ajudarà a recuperar informació de Moodle.';
$string['fullwelcome2'] = 'Per a cercar un recurs, utilitza la paraula clau "Recurs", 
        seguit d\'allò que vulguis cercar. Per exemple: "Recurs prova"';
$string['fullfallback'] = 'Ho sento, no he entès què has dit.';
$string['fullresourcematch'] = 'Del tipus "{$a}", s\'han trobat les següents coincidencies:';
$string['fullnoresourcematch'] = 'No s\'han trobat coincidències';
$string['fullaskresourcename'] = 'Quin nom té el recurs?';
$string['fullaskresourcetype'] = 'Quin tipus de recurs vols?';
$string['fullaskresourcecourse'] = 'De quin curs?';
$string['buttonall'] = 'Tots';

//Components de missatges estàtics
$string['compresourcematchcourse'] = ' - Curs: ';

//Peticions úniques a escoltar
$string['hearingresourcerequest'] = '.*(Busca(?<restype1> recurs| fitxer| url| tasca)?|(Busca )?(?<restype2>recurs|fitxer|url|tasca)) (?<resname>.*)';

//Conversacions a escoltar
$string['hearingresourceconver'] = '(?<restype2>busca|recurs|fitxer|url|tasca)';

//Esdeveniments
$string['resourcesearchevent'] = 'S\'ha cercat recurs';
$string['fallbackevent'] = 'S\'ha realitzat una petició desconeguda';