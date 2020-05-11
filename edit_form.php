<?php

class block_xatbot_edit_form extends block_edit_form {
	
	protected function specific_definition($mform) {
		$mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

		$mform->addElement('text', 'config_text', get_string('blockstring', 'block_simplehtml'));
		$mform->setDefault('config_text', 'default value');
		$mform->setType('config_text', PARAM_RAW);
	}

}
