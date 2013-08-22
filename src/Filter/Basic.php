<?php

/**
 *
 * @author Oleku Konko
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/olekukonko/SuperVariable/
 *      
 */
namespace Oleku\SuperVarriable\Filter;


class Basic implements Parsable {
	
	/**
	 * FILTER CONSTANTS
	 */
	const FILTER_NONE = 1;
	const FILTER_XSS = 2;
	const FILTER_SQL = 4;
	const FILTER_HIGH = 8;
	const FILTER_LOW = 16;
	const FILTER_HIGH_LOW = 32;
	const FILTER_INT = 64;
	const FILTER_FLOAT = 128;
	const FILTER_ALL = 63;
	/**
	 * IGNORE CONSTANTS
	 */
	const IGNORE_HEX = 1;
	const IGNORE_BASE64 = 2;
	const IGNORE_BINARY = 4; // use carefully can be exploited
	private $flags;
	private $ignore;

	/**
	 *
	 * @param int $flags
	 * @param int $ignore
	 */
	function __construct($flags = Basic::FILTER_NONE, $ignore = 0) {
		$this->flags = $flags;
		$this->ignore = $ignore;
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
	function parse($key, $mixed) {
		if (is_string($mixed)) {
			
			/**
			 * Only work in printable string
			 * Note : This can be exploited use carefully
			 * "asdf\n\r\t" would been seen as binary because not all characters
			 * are printable but
			 * "LKA#@%.54" would be seen as normal string;
			 */
			if (($this->ignore & self::IGNORE_BINARY) && ! ctype_print($mixed)) {
				return $mixed;
			}
			
			// Check if its Hex md5, sha1 etc all use hex
			if (($this->ignore & self::IGNORE_HEX) && ctype_xdigit($mixed)) {
				return $mixed;
			}
			
			// You can also ignore base 64 images
			if (($this->ignore & self::IGNORE_BASE64) && base64_encode(base64_decode($mixed)) === $mixed) {
				return $mixed;
			}
			
			// var_dump($this->flags & self::FILTER_XSS);
			// var_dump($this->flags & self::FILTER_LOW);
			// var_dump($this->flags & self::FILTER_INT);
			
			// General Filters
			$this->flags & self::FILTER_XSS and $mixed = htmlspecialchars($mixed, ENT_QUOTES, 'UTF-8');
			$this->flags & self::FILTER_SQL and $mixed = $this->escape($mixed);
			
			// Working with Strings
			$this->flags & self::FILTER_HIGH and $mixed = filter_var($mixed, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_HIGH);
			$this->flags & self::FILTER_LOW and $mixed = filter_var($mixed, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
			$this->flags & self::FILTER_HIGH_LOW and $mixed = filter_var($mixed, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_HIGH | FILTER_FLAG_ENCODE_LOW);
			
			// Working with numbers
			$this->flags & self::FILTER_INT and $mixed = filter_var($mixed, FILTER_SANITIZE_NUMBER_INT);
			$this->flags & self::FILTER_FLOAT and $mixed = filter_var($mixed, FILTER_SANITIZE_NUMBER_FLOAT);
			
			return $mixed;
		}
		
		// Recursive array filter
		if (is_array($mixed)) {
			$all = array();
			foreach($mixed as $k => $data) {
				$all[$k] = $this->parse($k, $data);
			}
			return $all;
		}
		
		// Recursive object filter
		if (is_object($mixed)) {
			$all = clone $mixed;
			foreach($mixed as $k => $data) {
				$all->{$k} = $this->parse($k, $data);
			}
			return $all;
		}
		
		return $mixed;
	}
}

?>