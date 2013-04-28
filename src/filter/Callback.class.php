<?php

/**
 *
 *
 * Copyright (c) 2013 Oleku Konko
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
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