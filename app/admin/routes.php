<?php
	// routes list

	//examples
	/*$Router->addRoute('/partners', 'Partners', 'index');
	$Router->addRoute('/partners/:id', array('id'), 'Partners', 'get');
	$Router->addRoute('/partners/save', 'Partners', 'save');
	$Router->addRoute('/partners/view/:text', array('template'), 'Partners', 'load');*/

	// default router to IndexController -> indexAction
	$Router->addRoute('/', 'Articles', 'index');
	$Router->addRoute('/login', 'Auth', 'login');
	$Router->addRoute('/auth', 'Auth', 'auth');
	$Router->addRoute('/logout', 'Auth', 'logout');



	$Router->defaultRoute();

	throw new \Admin\Exception\PageNotFound();

?>