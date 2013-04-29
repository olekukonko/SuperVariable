<?php

/**
 * 
 * @author Oleku Konko
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/olekukonko/SuperVariable/
 *        
 */
namespace super\filter;

use super\filter\Parsable;

class Callback implements Parsable {
	const USE_KEY = 1;
	const USE_VALUE = 2;
	private $flags;
	private $registry = array();
	private $match = array();

	/**
	 * Add callback to registry
	 * @param mixed $key
	 * @param callable $callback
	 */
	public function add($key, Callable $callback) {
		$this->registry[$key] = $callback;
	}

	/**
	 *
	 * @param String $regex
	 * @param callable $callback
	 * @param int $flag
	 */
	public function match($regex, Callable $callback, $flag = Callback::USE_KEY) {
		$condition = new \stdClass();
		$condition->regex = $regex;
		$condition->callback = $callback;
		$condition->flag = $flag;
		$this->match[] = $condition;
	}

	/**
	 *
	 * @param string $var
	 * @return number
	 */
	public function isRegex($var) {
		$regex = '/^((?:(?:[^?+*{}()[\]\\|]+|\\.|\[(?:\^?\\.|\^[^\\]|[^\\^])(?:[^\]\\]+|\\.)*\]|\((?:\?[:=!]|\?<[=!]|\?>)?(?1)??\)|\(\?(?:R|[+-]?\d+)\))(?:(?:[?+*]|\{\d+(?:,\d*)?\})[?+]?)?|\|)*)$/';
		return preg_match($regex, $var);
	}

	/**
	 * Simple Parser
	 * @param unknown $mixed
	 * @return mixed multitype:Ambigous
	 */
	public function parse($key, $mixed) {
		if (isset($this->registry[$key])) {
			$mixed = call_user_func($this->registry[$key], $key, $mixed);
		}
		
		if (count($this->match) > 0) {
			foreach ( $this->match as $condition ) {
				if ($condition->flag & Callback::USE_KEY) {
					var_dump($condition->callback);
					preg_match($condition->regex, $key) and $mixed = call_user_func($condition->callback, $key, $mixed);
				}
				
				if ($condition->flag & Callback::USE_VALUE) {
					is_string($mixed) and preg_match($condition->regex, $mixed) and $mixed = call_user_func($condition->callback, $key, $mixed);
				}
			}
		}
		
		return $mixed;
	}
}
?>