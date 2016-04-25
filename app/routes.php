<?php
// routes list

//examples
/*$Router->addRoute('/partners', 'Partners', 'index');
$Router->addRoute('/partners/:id', array('id'), 'Partners', 'get');
$Router->addRoute('/partners/save', 'Partners', 'save');
$Router->addRoute('/partners/view/:text', array('template'), 'Partners', 'load');*/

// default router to IndexController -> indexAction
$Router->addRoute('/');
$Router->addRoute('/work', 'Work', 'index');
$Router->addRoute('/about', 'About', 'index');
$Router->addRoute('/allabout', 'Allabout', 'index');
$Router->addRoute('/spec', 'Spec', 'index');
$Router->addRoute('/contacts', 'Contacts', 'index');
$Router->addRoute('/help', 'Help', 'index');
$Router->addRoute('/spec', 'Spec', 'index');
$Router->addRoute('/sitemap', 'Sitemap', 'index');
$Router->addRoute('/news/:url', array('news'), 'News', 'index');
$Router->addRoute('/getnews/:id', array('number'), 'News', 'getlastNews');
$Router->addRoute('/freelancers/:url', array('service'), 'Freelancers', 'index');
$Router->addRoute('/listfreelancers', 'ListFreelancers', 'index');
$Router->addRoute('/calc', 'Calc', 'index');
$Router->addRoute('/reviews', 'Freelancers', 'reviews');
$Router->addRoute('/reviews/add', 'Freelancers', 'reviewsAdd');
$Router->addRoute('/projects/add', 'Index', 'projectAdd');
$Router->addRoute('/projects/info', 'Index', 'projectsInfo');
$Router->addRoute('/projects/offer_add', 'Index', 'projectOfferAdd');
$Router->addRoute('/search', 'Index', 'search');
$Router->addRoute('/chat', 'Chat', 'messages');
$Router->addRoute('/chat/send', 'Chat', 'sendMessage');

$Router->addRoute('/respassword', 'signup', 'respassword');

//profile
$Router->addRoute('/signup', 'Signup', 'reg');
$Router->addRoute('/signup/ckeck_email', 'Signup', 'ckeckEmail');
$Router->addRoute('/login', 'Signup', 'login');
$Router->addRoute('/logout', 'Signup', 'logout');
$Router->addRoute('/edit', 'Edit', 'index');
$Router->addRoute('/edit/calendar', 'Edit', 'calendar');
$Router->addRoute('/edit/deilfile', 'Edit', 'delFile');
$Router->addRoute('/edit/uploadfile', 'Upload', 'upImage');
$Router->addRoute('/profile/:id', array('userId'), 'Profile', 'index');


$clazz = \App::$state->app . '\Exception\PageNotFound';
throw new $clazz();

?>