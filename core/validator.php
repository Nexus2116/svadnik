<?php

namespace Core {

	use Closure;

	class Validator {

		public $errors = null;

		private $rules = array ();
		private $field;
		private $message;

		public function __construct() {
			$this->types = array (
				'int' => function ($value) {
					return is_numeric($value) && (int) $value == $value ? true : false;
				},
				'float' => function($value) {
					return is_numeric($value) && (float) $value == $value ? true : false;
				},
				'alpha' => '[A-zА-я]+',
				'alphanum' => '[A-zА-я0-9__]+',
				'email' => '[A-z0-9_]+([.-][A-z0-9_-]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}'
			);	
		}

		public function set($type, $rule) {
			$this->types[$type] = $rule;
		}

		private function notValid() {
			$this->errors[$this->field] = $this->message;
			return false;
		}

		private function required($value) {
			if(empty($value))
				return $this->notValid();
		}

		private function max($value, $val) {
			if($value > $val)
				return $this->notValid();
		}

		private function min($value, $val) {
			if($value < $val)
				return $this->notValid();
		}

		private function strlen($value, array $val) {
			$len = mb_strlen($value);
			if($val[1] == '*')
				$val[1] = PHP_INT_MAX;
			if($len < $val[0] || $len > $val[1])
				return $this->notValid();
		}

		private function in($value, array $vals) {
			if(!in_array($value, $vals))
				return $this->notValid();
		}

		private function not_in($value, array $vals) {
			if(in_array($value, $vals))
				return $this->notValid();
		}

		private function match($value, $val) {
			if(!preg_match('/^' . $val . '$/', $value))
				return $this->notValid();
		}

		private function between($value, array $vals) {
			$first = $vals[0];
			$last = $vals[1];

			if($value < $first || $value > $last)
				return $this->notValid();
		}

		private function type($value, $type) {
			if(is_object($this->types[$type]) && get_class($this->types[$type]) == 'Closure') {
				if($this->types[$type]($value) === false)
					return $this->notValid();
			} else {
				$regexp = '/^'.$this->types[$type].'$/Ui';
				if(!preg_match($regexp, $value))
					return $this->notValid();
			}
			return true;
		}

		private function check($value, $rule) {

			$itemRules = $rule[0];
			$errors = $rule[1];

			$rules = explode ('|', $itemRules);

			$errorIndex = 0;
			foreach ($rules as $rule) {
				$params = explode(':', $rule);
				$criteria = $params[0];
				$values = array ();

				if(isset($params[1])) {
					$values = explode(',', $params[1]);
					if(count($values) == 1)
						$values = $values[0];
				}

				$this->message = false;
					if(isset($errors[$errorIndex]))
						$this->message = $errors[$errorIndex];
				$errorIndex++;

				if($this->{$criteria}($value, $values) === false)
					return false;
			}
		}

		public function validate($data, $rules) {
			$this->errors = null;
			$this->rules = array ();
			$this->message = null;

			$this->rules = $rules;

			foreach ($data as $key => $value) {
				if(isset($this->rules[$key])) {
					$this->field = $key;
					$this->check($value, $this->rules[$key]);
				}
			}

			if($this->errors === null)
				return true;

			return false;

		}
	}
}?>