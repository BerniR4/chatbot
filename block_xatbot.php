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

		$file = $this->manageFile();
		
		$filteropt = new stdClass;
		$filteropt->overflowdiv = true;
		$filteropt->noclean = true;
		$filteropt->filter = false;
		
		$this->content 			= new stdClass;

		$this->content->text 	= format_text($file->get_content(), FORMAT_HTML, $fileropt);	
		$this->content->footer 	= '';
		$this->page->requires->css('/blocks/xatbot/style.css');
		//$this->page->requires->js_call_amd('blocks/xatbot', '/blocks/xatbot/index.js');
		return $this->content;
	}

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
			
	}
}
