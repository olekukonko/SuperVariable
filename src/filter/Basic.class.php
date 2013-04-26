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

class Basic implements Parsable {
	const FILTER_NONE = 1;
	const FILTER_XSS = 2;
	const FILTER_SQL = 4;
	const FILTER_HIGH = 8;
	const FILTER_LOW = 16;
	const FILTER_HIGH_LOW = 32;
	const FILTER_INT = 64;
	const FILTER_ALL = 63;
	private $flags;

	function __construct($flags = Basic::FILTER_NONE) {
		$this->flags = $flags;
		// var_dump(1 | 2 | 4 | 8 | 16 | 32 , 63 & 63);
	}

	/**
	 * SQL Filter
	 * @param string $value
	 * @return string
	 */
	function escape($value) {
		if ($value = @mysql_real_escape_string($value))
			return $value;
		$return = '';
		for($i = 0; $i < strlen($value); ++ $i) {
			$char = $value[$i];
			$ord = ord($char);
			if ($char !== "'" && $char !== "\"" && $char !== '\\' && $ord >= 32 && $ord <= 126)
				$return .= $char;
			else
				$return .= '\\x' . dechex($ord);
		}
		return $return;
	}

	/**
	 * Simple Parser
	 * @param unknown $mixed
	 * @return mixed multitype:Ambigous
	 */
	function parse($mixed) {
		if (is_string($mixed)) {
			
			// var_dump($this->flags & self::FILTER_XSS);
			// var_dump($this->flags & self::FILTER_LOW);
			// var_dump($this->flags & self::FILTER_INT);
			
			$this->flags & self::FILTER_XSS and $mixed = htmlspecialchars($mixed, ENT_QUOTES, 'UTF-8');
			$this->flags & self::FILTER_SQL and $mixed = $this->escape($mixed);
			$this->flags & self::FILTER_HIGH and $mixed = filter_var($mixed, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_HIGH);
			$this->flags & self::FILTER_LOW and $mixed = filter_var($mixed, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
			$this->flags & self::FILTER_HIGH_LOW and $mixed = filter_var($mixed, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_HIGH | FILTER_FLAG_ENCODE_LOW);
			$this->flags & self::FILTER_INT and $mixed = filter_var($mixed, FILTER_SANITIZE_NUMBER_INT);
			return $mixed;
		}
		
		if (is_array($mixed)) {
			$all = array();
			foreach ( $mixed as $data ) {
				$all[] = $this->parse($data);
			}
			return $all;
		}
		return $mixed;
	}
}

?>