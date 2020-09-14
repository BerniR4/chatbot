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
 * The block chatbot
 *
 * @package    block_chatbot
 * @copyright  2020 Daniel Amo, Bernat Rovirosa
 *  daniel.amo@salle.url.edu
 * @copyright  2020 La Salle Campus Barcelona, Universitat Ramon Llull https://www.salleurl.edu
 * @author     Daniel Amo
 * @author     Bernat Rovirosa
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_chatbot extends block_base {
	public function init() {
		$this->title = get_string('chatbot', 'block_chatbot');
	}

	function hide_header() {
		return TRUE;
	}

	public function get_content() {
		if ($this->content !== null) {
			return $this->content;
		}

		$this->content->text = $this->get_html();
		$this->content->footer 	= '';
		$this->page->requires->css(new moodle_url($CFG->wwwroot . '/blocks/chatbot/style.css'));
		$this->page->requires->jquery();
		
		$jsmodule = array(
			'name' => 'module',
			'fullpath' => '/blocks/chatbot/module.js',
			'requires' => array(),
			'strings' => array()
		);
		$this->page->requires->js_init_call('M.block_chatbot.init', array($this->uuidv4(), $this->page->context->id, 
			$this->page->course->id), false, $jsmodule);
		return $this->content;
	}

	/**
	 * Returns the HTML that defines the chat
	 * 
	 * @return string
	 */
	private function get_html() {
		global $CFG;
		return 	'<!DOCTYPE html>'
			.'<html>'
				.'<div class="m_xat-body">'
					.'<div class="m_xat-headerBar">'
						.'<div class="m_xat-user-photo"><img src="' . $CFG->wwwroot . '/blocks/chatbot/images/bot_img.jpg"></div>'
						.'<p class="m_xat-title">LSBot</p>'
					.'</div>'
					.'<div class="m_xat-box">'
						.'<div class="m_xat-logs">'
							.'<div class="m_xat m_xat-bot" id="m_xat-loadingGif" style="display: none;">'
								.'<div class="m_xat-gif"><img src="' . $CFG->wwwroot . '/blocks/chatbot/images/loading.gif"></div>'
							.'</div>'
						.'</div>'
					.'</div>'
					.'<div class="m_xat-form">'
						.'<div id="m_xat-inputDiv">'
							.'<div id="m_xat-buttonDiv"></div>'
							.'<textarea class="m_xat-input" placeholder="Escriu un missatge..." rows="1" data-min-rows="1"></textarea>'
						.'</div>'
						.'<div id="m_xat-form-buttons">'
							.'<input width="40" height="40" type ="image" id="m_xat-rec" src="' . $CFG->wwwroot . '/blocks/chatbot/images/send.png">'
						.'</div>'
					.'</div>'

				.'</div>'
			.'</html>';
	}

	/**
	 * Returns an universal unique identifier
	 * 
	 * @return string
	 */
	function uuidv4() {
		return implode('-', [
			bin2hex(random_bytes(4)),
			bin2hex(random_bytes(2)),
			bin2hex(chr((ord(random_bytes(1)) & 0x0F) | 0x40)) . bin2hex(random_bytes(1)),
			bin2hex(chr((ord(random_bytes(1)) & 0x3F) | 0x80)) . bin2hex(random_bytes(1)),
			bin2hex(random_bytes(6))
    	]);
	}

	function applicable_formats() {
		return array(
			'all' => true, 
		);
	}

}
