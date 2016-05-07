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

$Router->addRoute('/spec', 'Spec', 'index');
$Router->addRoute('/contacts', 'Contacts', 'index');
$Router->addRoute('/help', 'Help', 'index');
$Router->addRoute('/spec', 'Spec', 'index');
$Router->addRoute('/sitemap', 'Sitemap', 'index');
$Router->addRoute('/freelancers/:url', array('service'), 'Freelancers', 'index');
$Router->addRoute('/listfreelancers', 'ListFreelancers', 'index');
$Router->addRoute('/calc', 'Calc', 'index');
$Router->addRoute('/reviews', 'Freelancers', 'reviews');
$Router->addRoute('/reviews/add', 'Freelancers', 'reviewsAdd');


$Router->addRoute('/respassword', 'signup', 'respassword');

//profile
$Router->addRoute('/signup', 'Signup', 'reg');
$Router->addRoute('/signup/ckeck_email', 'Signup', 'ckeckEmail');
$Router->addRoute('/login', 'Signup', 'login');
$Router->addRoute('/logout', 'Signup', 'logout');
$Router->addRoute('/edit', 'Edit', 'index');
$Router->addRoute('/edit/calendar', 'Edit', 'calendar');
$Router->addRoute('/edit/deilfile', 'Edit', 'delFile');
$Router->addRoute('/uploadfile', 'Upload', 'upImage');

$Router->addRoute('/profile/:id', array('userId'), 'Profile', 'index');


// new
$Router->addRoute('/executor/', 'Executor', 'index');
$Router->addRoute('/executor/:id', ['user_id'], 'Executor', 'user_profile');
$Router->addRoute('/executor/upload_avatar', 'Executor', 'upload_avatar');
$Router->addRoute('/executor/change_profile', 'Executor', 'change_profile');
$Router->addRoute('/executor/change_pswd', 'Executor', 'change_pswd');
$Router->addRoute('/executor/services_save', 'Executor', 'services_save');
$Router->addRoute('/executor/service_delete', 'Executor', 'service_delete');
$Router->addRoute('/executor/photo_upload', 'Executor', 'photo_upload');
$Router->addRoute('/executor/photo_delete', 'Executor', 'photo_delete');
$Router->addRoute('/executor/ajax/service_inputs/:id', ['id'], 'Executor', 'service_inputs');
$Router->addRoute('/executor/upload_video', 'Executor', 'upload_video');
$Router->addRoute('/executor/delete_video', 'Executor', 'delete_video');
$Router->addRoute('/executor/upload_presentation', 'Executor', 'upload_presentation');
$Router->addRoute('/executor/delete_presentation', 'Executor', 'delete_presentation');
$Router->addRoute('/executor/reserve', 'Executor', 'reserve');
$Router->addRoute('/executor/change_to_order', 'Executor', 'to_order_status');
$Router->addRoute('/executor/calendar_reserve', 'Executor', 'calendar_reserve');

$Router->addRoute('/service/list_executors/:url/count', ['service'], 'Service', 'list_executors_count');
$Router->addRoute('/service/list_executors/:url', ['service'], 'Service', 'list_executors');
$Router->addRoute('/service/:url', ['service'], 'Service', 'index');

$Router->addRoute('/customer/', 'Customer', 'index');
$Router->addRoute('/customer/change_pswd', 'Customer', 'change_pswd');
$Router->addRoute('/customer/change_profile', 'Customer', 'change_profile');
$Router->addRoute('/customer/avatar_upload', 'Customer', 'avatar_upload');
$Router->addRoute('/customer/:id', ['user_id'], 'Customer', 'user_profile');
$Router->addRoute('/customer/send_message', 'Customer', 'send_message');
$Router->addRoute('/customer/user_remove_project', 'Customer', 'user_remove_project');

$Router->addRoute('/projects/subscribe-project', 'Projects', 'subscribe_project');
$Router->addRoute('/executor/user_remove_project', 'Projects', 'user_remove_project');
$Router->addRoute('/projects/add_project', 'Projects', 'add_project');
$Router->addRoute('/projects/get_information', 'Projects', 'get_information');
$Router->addRoute('/project/to_order', 'Projects', 'to_order');

$Router->addRoute('/allabout', 'Allabout', 'index');
$Router->addRoute('/allabout/create_article', 'Allabout', 'create_article');
$Router->addRoute('/allabout/:id', ['id'], 'Allabout', 'page_article');
$Router->addRoute('/allabout/get_articles/:id', ['offset'], 'Allabout', 'get_articles');

$Router->addRoute('/search', 'Search', 'index');
$Router->addRoute('/search/users', 'Search', 'users');

$Router->addRoute('/news/:url', ['news'], 'News', 'index');
$Router->addRoute('/getnews/:id', ['number'], 'News', 'getlastNews');

$Router->addRoute('/chat/get_messages/:id', ['id'], 'Chat', 'get_messages');
$Router->addRoute('/chat/send_message', 'Chat', 'send_message');


$clazz = \App::$state->app . '\Exception\PageNotFound';
throw new $clazz();

?>