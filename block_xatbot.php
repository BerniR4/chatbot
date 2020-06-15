<?php
class block_xatbot extends block_base {
	public function init() {
		$this->title = get_string('xatbot', 'block_xatbot');
	}

	function hide_header() {
		return TRUE;
	}

	public function get_content() {
		if ($this->content !== null) {
			return $this->content;
		}

		//$file = $this->manageFile();

		//$this->content->text = $file->get_content();
		$this->content->text = $this->get_http();

		$this->content->footer 	= '';
		$this->page->requires->css(new moodle_url($CFG->wwwroot . '/blocks/xatbot/style.css'));
		$this->page->requires->jquery();
		$this->page->requires->js(new moodle_url($CFG->wwwroot . '/blocks/xatbot/index.js'));
		//$this->page->requires->js_call_amd('blocks/xatbot', '/blocks/xatbot/index.js');
		return $this->content;
	}

	private function get_http() {
		return 	'<!DOCTYPE html>'
			.'<html>'
				.'<div class="m_xat-body">'
					.'<div class="m_xat-headerBar">'
						.'<div class="m_xat-user-photo"><img src="../blocks/xatbot/images/bot_img.jpg"></div>'
						.'<p class="m_xat-title">LSBot</p>'
					.'</div>'
					.'<div class="m_xat-box">'
						.'<div class="m_xat-logs">'
							.'<div class="m_xat m_xat-bot" id="m_xat-loadingGif" style="display: none;">'
								.'<div class="m_xat-gif"><img src="../blocks/xatbot/images/loading.gif"></div>'
							.'</div>'
						.'</div>'
					.'</div>'
					.'<div class="m_xat-form">'
						.'<div id="m_xat-inputDiv">'
							.'<div id="m_xat-buttonDiv"></div>'
							.'<textarea class="m_xat-input" placeholder="Escriu un missatge..." rows="1" data-min-rows="1"></textarea>'
						.'</div>'
						.'<div id="m_xat-form-buttons">'
							.'<input width="40" height="40" type ="image" id="m_xat-rec" src="../blocks/xatbot/images/send.png">'
						.'</div>'
					.'</div>'

				.'</div>'
			.'</html>';
	}
/*
	public function manageFile () {	
		global $CFG;
		$fs = get_file_storage();

		// Prepare file record object
		$fileinfo = array(
				'contextid' => $this->context->id, // ID of context
				'component' => 'block_xatbot',     // usually = table name
				'filearea' => 'content',     // usually = table name
				'itemid' => 0,               // usually = ID of row in table
				'filepath' => '/',
				'filename' => 'xat.html');

		$file = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'],
				$fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename']);

		if ($file) {
			//		$sha1 = sha1_file('/blocks/xatbot/xat.html');
			$file->delete();		
			//		if ($file->get_contenthash() == 
			//		return $file;
		}

		$fs->create_file_from_pathname($fileinfo, $CFG->dirroot . '/blocks/xatbot/xat.html');

		$file = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'],
				$fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename']);
		return $file;

	}*/
}
