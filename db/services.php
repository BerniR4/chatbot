<?php

$functions = array(
	'block_xatbot_test_call' => array(
		'classname'		=> 'external',
		'methodname'	=> 'test_call',
		'classpath'		=> 'blocks/xatbot/classes/external.php',
		'description'	=> 'De moment és una prova, no et calentis',
		'type'			=> 'read',
		'ajax'			=> true
	)
);

$services = array(
	'call_test_service'	=> array(
		'functions' 	=> array ('block_xatbot_test_call'),
		'requiredcapability' => '',
		'restrictedusers' => 0,
		'enabled' => 1,
		'shortname' => '',
		'downloadfiles' => 0,
		'uploadfiles' => 0
	)
);
