<?php
	
namespace Model;

	class Settings extends \Core\Model {

	    public $timestamps = false;

	    public static function floorOptions() {
	    	$options = \Model\Settings::where('key', 'fields')->first();
	    	if($options == null)
	    		return array ();
	    	
	    	$fields = explode(',', $options->value);

	    	$data = array ();
	    	foreach ($fields as $item) {
	    		$trimed = trim($item);
	    		$key = md5($trimed);
	    		$data[$key] = $trimed;
	    	}

	    	\App::config('plan', $data);
	    }

	}

?>