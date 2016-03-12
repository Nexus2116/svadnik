<?php
	
	// Admin config file
	$Config = &App::config();

	$Config->sidebar = array ('articles', 'settings', 'help');

	// set types of pages
	$Config->pages = array (
		'articles' => 'Статья',
	);

	$Config->types = array ();

	$Config->techTypes = array ();


	// set image scales for types
	$Config->scales = array (
		'articles' => array (
			'admin' => array (10000, 200),
			'big' => array (300, 300),
			'gallery' => array (500, 500),
			'blur' => array (null, 700),
			'projects_info' => array (53, 51)
		)
	);
	
?>