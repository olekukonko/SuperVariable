<?php

/**
 * Copyright 2012 Oleku Konko
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 *
 * @author oleku
 *        
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