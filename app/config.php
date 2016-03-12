<?php
	
	// Admin config file
	$Config = &App::config();

		// set image scales for types
	$Config->scales = array (
		'articles' => array (
			'admin' => array (10000, 200),
			'big' => array (300, 300),
			'gallery' => array (500, 500),
			'blur' => array (null, 700),
			'projects_info' => array (53, 51)
		),

		'avatar' => array (
			'admin' => array (10000, 200),
			'big' => array (300, 300)
		)
	);
	
?>