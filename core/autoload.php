<?php

namespace Core {

	class Autoload {

		private $defaultNamespace = '';

	    // namespaces class map
	    protected $namespacesMap = array();

	    public function __construct($defaultNamespace = 'core', $path) {
			$this->defaultNamespace = $defaultNamespace;

			$this->addNamespace($this->defaultNamespace, $path);
		}

	    public function addNamespace($namespace, $rootDir) {
	    	$namespace = str_replace('\\', '/', strtolower($namespace));
	    	$rootDir = strtolower($rootDir);

			$this->namespacesMap[$namespace] = $rootDir;
	    }
	    
	    public function register() {
	        spl_autoload_register(array($this, 'autoloader'));
	    }
	    
	    protected function autoloader($name) {
	    	$name = strtolower($name);
	        $pathParts = explode('\\', $name);

	        $class = array_pop($pathParts);
	        $namespace = join('/', $pathParts);

	        if($namespace == '')
	        	$namespace = $this->defaultNamespace;

            if (isset($this->namespacesMap[$namespace]))
            	$filePath = $this->namespacesMap[$namespace] . '/' . $class . '.php';
	        else
	        	$filePath = DOCROOT .'/'. $namespace . '/' . $class . '.php';

        	include_once $filePath;
	    }
 
	}

}

?>