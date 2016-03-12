<?php

namespace Core {

	class View extends Magic {
		
		public $viewFolder = '/view/';

		public $layoutFolder = '/layouts';

		public $layout = 'index'; // main layout file name

		public $css = array(); // array of css files

		public $js = array(); // array of js files 

		public $partial; // current template

		public $space = ''; // application. if (app == 'app') $space = ''

		public $html;

		//public $output = true;

		public function __construct() {
			$this->space = \App::$state->app;

			include_once (APPPATH . '/helper.php');
			$this->html = new \HTML();

			$this->viewFolder = APPPATH . $this->viewFolder;
		}

		public function includeCss() {
			foreach ($this->css as $css)
				echo '<link rel="stylesheet" type="text/css" href="/public/css/'.$css.'.css">' . "\n";
		}

		public function includeJs() {
			foreach ($this->js as $js)
				echo '<script type="text/javascript" src="/public/js/'.$js.'.js"></script>' . "\n";
		}

		public function renderLayout() {
			include_once($this->viewFolder.$this->layoutFolder.'/'.$this->layout.'.phtml');
		}

		public function renderFile($file) {
			include ($this->viewFolder . $file . '.phtml');	
		}

		// render current controllers templates
		public function render($actionName = '') {
			if($actionName == '') {
				$actionName = \App::route('action');

				if($this->partial != '')
					$actionName = $this->partial;
			}

			$controllerName = \App::route('controller');

			$path = $this->viewFolder . strtolower($controllerName.'/'.$actionName).'.phtml';
			include($path);
		}

		// render other controllers templates
		public function renderPartial($controller, $action = '') {
			if($controller == null) 
				return;

			if($action == '')
				$action = 'index';

			$viewPath = $this->viewFolder . strtolower($controller . '/' . $action);
			include($viewPath . '.phtml');
		}

		// get bufered content of template or file
		public function getContent($file) {
			if($file == null)
				return null;
			ob_start();
			$this->renderFile($file);
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}

	}
}

?>
