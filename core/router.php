<?php

namespace Core {

	class Router {

		public $app = 'app';
		public $apps = array();
		public $lang;
		public $urlParts = array();
		public $langs = array ();

		private $urlHost;
		private $urlRequest;
		private $urlPartsCount;
		private $routeParts = array();
		private $routePartsCount;
		private $route;

		public $routeMasks = array(
			':id' => '\d+',
			':tiny' => '(-|_)?[a-z0-9]+(-|_)?',
			':url' => '[a-z0-9_-]+',
			':int' => '-?[1-9]\d+',
			':text' => '[a-z]+',
			':any' => '.+',
			':info' => 'info'
		);

		public function addLang($langs) {
			$this->langs = array_merge($this->langs, (array) $langs);
		}

		public function setLang($langKey) {
			if(isset($this->langs[$langKey]))
				$this->lang = $langKey;
			else
				$this->lang = 'ru';
		}

		public function __construct(array $subapps) {
			$this->apps = $subapps;
			$this->route = new Route();

			$this->route->ajax = false;
			if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
				$this->route->ajax = true;
		}

		public function parseUrl() {
			$this->urlHost = $_SERVER['HTTP_HOST'];
			$this->urlRequest = $_SERVER['REQUEST_URI'];
			$pos = strpos($this->urlRequest, '?');
			if ($pos !== false)
				$this->urlRequest = substr($this->urlRequest, 0, $pos);
			$this->urlRequest = trim($this->urlRequest, '/');
			$this->route->host = 'http://' . $this->urlHost;
			$this->route->request = '/' . $this->urlRequest;
			$this->route->url = $this->route->host . $this->route->request;
			$this->urlParts = explode('/', $this->urlRequest);


			if (in_array($this->urlParts[0], $this->apps))
				$this->app = array_shift($this->urlParts);

			if (in_array(@$this->urlParts[0], array_keys($this->langs)))
				$this->lang = array_shift($this->urlParts);

			$this->route->urlParts = $this->urlParts;
			$this->route->lang = $this->lang;

			$this->urlRequest = join('/', $this->urlParts);

			$this->urlPartsCount = count($this->urlParts);

			if(@$this->urlParts[0] == '')
				$this->urlPartsCount = 0;

			$urlHost = explode('.', $this->urlHost);
			$uhZone = array_pop($urlHost);
			$uhDomain = array_pop($urlHost);
			$this->route->mainHost = 'http://' . $uhDomain . '.' . $uhZone;
			$this->route->subdomain = (end($urlHost)) ? end($urlHost) : '';
		}

		public function addMask($mask, $maskRegExp) {
			$this->routeMasks[$mask] = $maskRegExp;
		}

		public function addRoute() {
			if (($argsCount = func_num_args()) > 0) {
				$args = func_get_args();
				$this->route->routeVars = array();
				$this->route->controller = 'Index';
				$this->route->action = 'index';
				$route = $args[0];
				$route = trim($route, '/');
				$this->routeParts = explode('/', $route);
				$this->routePartsCount = $this->routeParts[0] != '' ? count($this->routeParts) : 0;

				if ($argsCount > 1) {
					$argKey = 1;
					if (is_array($args[1])) {
						$this->route->routeVars = $args[1];
						$argKey++;
					}

					if ($argsCount > $argKey) {
						$this->route->controller = $args[$argKey++];
						if ($argsCount > $argKey) {
							$this->route->action = $args[$argKey++];
							if (!empty($_POST)) {
								if ($argsCount > $argKey)
									$this->route->action = $args[$argKey];
								else
									$this->route->action .= '_post';
							}
						}
					}
				}

				if ($this->routePartsCount == $this->urlPartsCount) {
					if (strpos($route, ':') === false) {
						if ($route == $this->urlRequest) {
							unset($this->route->routeVars);
							\App::set('route', $this->route);
							new Dispatcher();
						}
					} else {
						if (preg_match($this->getRouteRegexp($route), $this->urlRequest)) {
							if ($this->routePartsCount != 0)
								$this->fillVars($this->route->routeVars);
							\App::set('route', $this->route);
							new Dispatcher();
						}
					}
				}
			}
		}

		public function defaultRoute() {
			$urlParts = $this->urlParts;//explode('/', $this->urlRequest);
			$this->route->controller = array_shift($urlParts);
			if($this->route->controller == '')
				$this->route->controller = 'index';
			$this->route->action = array_shift($urlParts);
			if($this->route->action == '')
				$this->route->action = 'index';

			if (!empty($_POST))
				$this->route->action .= '_post';

			while ($urlPart = array_shift($urlParts)) {
				$this->route->$urlPart = @array_shift($urlParts);
				if($this->route->$urlPart == null)
					$this->route->$urlPart = true;
			}
			\App::set('route', $this->route);

			new Dispatcher();
		}

		public function articleRoute() {
			$urlParts = $this->urlParts;//explode('/', $this->urlRequest);
			$this->route->controller = 'Articles';
			$this->route->action = 'get';
			while ($urlPart = array_shift($urlParts))
				$this->route->data[] = $urlPart;

			\App::set('route', $this->route);

			new Dispatcher();
		}

		private function fillVars() {
			$i = 0;
			for ($j = 0; $j < $this->routePartsCount; $j++) {
				$routeVar = $this->routeParts[$j];
				if (strpos($routeVar, ':') !== false) {
					if (!array_key_exists($i, $this->route->routeVars)) {
						$routeMask = substr($routeVar, 1);
						$k = 1;
						while (array_key_exists(($routeVar = $routeMask. $k), $this->route->vars))
							$k++;
					} else
						$routeVar = $this->route->routeVars[$i];
					$i++;
				}
				$this->route->$routeVar = $this->urlParts[$j];
			}
			unset($this->route->routeVars);
		}

		private function getRouteRegexp($route) {
			$routeMasks = array_keys($this->routeMasks);
			$regExps = array_values($this->routeMasks);
			$re = '#^' . str_replace($routeMasks, $regExps, $route) . '$#i';
			return $re;
		}
	}

}

?>
