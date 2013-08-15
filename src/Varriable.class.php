<?php

/**
 *
 * @author Oleku Konko
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/olekukonko/SuperVariable/
 *      
 */
namespace super;

use super\filter\Parsable;

class Varriable implements \ArrayAccess, \IteratorAggregate, \JsonSerializable {
	const ALLOW_SET = 1; // Allow you to set varraible (Disabled by Default)
	const ALLOW_GET = 2; // Allow you to get varriable (Enabled By Default)
	const ALLOW_SET_GET = 3;
	const ALLOW_NONE = 4;
	
	// Data Storage
	private $data = array();
	private $ignore = array();
	private $offsetFilter = array();
	private $flags;
	private $filter = null;
	private $default = null;

	/**
	 *
	 * @param array $array
	 * @param Parsable $filter
	 * @param int $flags
	 */
	public function __construct($data, Parsable $filter = null, $flags = Varriable::ALLOW_GET) {
		// Check if Traversable
		$t = $data instanceof \Traversable;
		
		if (! is_array($data) and $k = ! $t)
			throw new \ErrorException("Only Arrays and Traversable allowed");
		
		$this->data = $t ? iterator_to_array($data) : $data;
		$this->flags = $flags;
		$this->filter = $filter;
	}

	/**
	 * Modify Default Values when Object is empty
	 * @param array $array
	 */
	public function setDefault($default) {
		$this->default = $default;
	}

	/**
	 * Modify Varriable Sata
	 * @param array $array
	 */
	public function setData(array $array) {
		$this->data = $array;
	}

	/**
	 * Modify Varriable Sata
	 * @param array $array
	 */
	public function ignore() {
		$this->ignore = array_fill_keys(func_get_args(), true);
	}

	/**
	 * Create a new iterator from an ArrayObject instance
	 * @see IteratorAggregate::getIterator()
	 * @link http://php.net/manual/en/arrayobject.getiterator.php
	 */
	public function getIterator() {
		return new \ArrayIterator($this->data);
	}

	/**
	 * This iterator allows to unset and modify values and keys while iterating
	 * over Arrays and Objects in the same way as the ArrayIterator.
	 * Additionally it is possible to iterate over the current iterator entry.
	 * @link http://php.net/manual/en/class.recursivearrayiterator.php
	 */
	public function getRecursiveIterator() {
		return new \RecursiveArrayIterator($this->data);
	}

	/**
	 * Set a filter for a particular offset
	 * @param mixed $offset
	 * @param Parsable $filter
	 * @throws \ErrorException
	 */
	public function offsetFilter($offset, Parsable $filter) {
		if (! $filter instanceof Parsable)
			throw new \ErrorException("Invalid filter added to list");
		$this->offsetFilter[$offset] = $filter;
	}

	/**
	 * utilized for reading data from inaccessible properties.
	 * @param mixed $offset
	 * @throws \ErrorException
	 * @link
	 *       http://www.php.net/manual/en/language.oop5.overloading.php#object.get
	 */
	public function __get($offset) {
		if ($this->flags ^ Varriable::ALLOW_GET)
			throw new \ErrorException("You are not alloowed to get individual elements");
		
		return $this->offsetGet($offset);
	}

	/**
	 * triggered when invoking inaccessible methods in an object context.
	 * @param mixed $offset
	 * @param mixed $value
	 * @link
	 *       http://www.php.net/manual/en/language.oop5.overloading.php#object.call
	 */
	public function __call($offset, $value) {
		if (count($value) > 0) {
			$data = $this->getValue($value);
			// var_dump($data);
		}
		return $this->offsetGet($offset);
	}

	/**
	 * triggered when invoking inaccessible methods in an object context.
	 * @param mixed $offset
	 * @param mixed $value
	 * @link
	 *       http://www.php.net/manual/en/language.oop5.overloading.php#object.call
	 */
	public function find($path) {
		$path = array_filter(explode(".", $path)); // remove white space
		if ($var = $this->offsetGet(array_shift($path))) {
			return $this->getValue($var, $path);
		}
		return $var;
	}

	/**
	 *
	 * @param string $path
	 * @param mixed $value
	 * @return unknown
	 */
	public function inject($path, $value) {
		$path = array_filter(explode(".", $path)); // remove white space
		$key = $this->putValue($this->data, $path, $value);
		
		// var_dump($this->data['object']);
		return $key;
	}

	/**
	 * method is called when a script tries to call an object as a function.
	 * @param string $offset
	 * @return Ambigous <NULL, multitype:>
	 * @link http://www.php.net/manual/en/language.oop5.magic.php#object.invoke
	 */
	public function __invoke($offset) {
		return $this->offsetGet($offset);
	}

	/**
	 * Sets the value at the specified index to newval
	 * @see ArrayAccess::offsetSet()
	 * @link http://php.net/manual/en/arrayobject.offsetset.php
	 */
	public function offsetSet($offset, $value) {
		if ($this->flags ^ Varriable::ALLOW_SET)
			throw new \ErrorException("Offset assignment disabled");
		
		$this->data[$offset] = $value;
	}

	/**
	 * Returns the value at the specified index
	 * @see ArrayAccess::offsetGet()
	 * @link http://www.php.net/manual/en/arrayobject.offsetget.php
	 */
	public function offsetGet($offset) {
		// check if you can get
		if ($this->flags ^ Varriable::ALLOW_GET) {
			throw new \ErrorException("Offset retrival disabled");
		}
		
		// Fild filter to use
		$filter = isset($this->offsetFilter[$offset]) ? $this->offsetFilter[$offset] : $this->filter;
		
		// Add ignore rule
		isset($this->ignore[$offset]) and $filter = null;
		
		// Illegal string-offset Fix
		return $this->offsetExists($offset) ? ($filter ? $filter->parse($offset, $this->data[$offset]) : $this->data[$offset]) : $this->default;
	}

	/**
	 * Returns whether the requested index exists
	 * @see ArrayAccess::offsetExists()
	 * @link http://www.php.net/manual/en/arrayobject.offsetexists.php
	 */
	public function offsetExists($offset) {
		return isset($this->data[$offset]);
	}

	/**
	 * Unsets the value at the specified index
	 * @see ArrayAccess::offsetUnset()
	 * @link http://www.php.net/manual/en/arrayobject.offsetunset.php
	 */
	public function offsetUnset($offset) {
		unset($this->data[$offset]);
	}

	/**
	 * Specify data which should be serialized to JSON
	 * @see JsonSerializable::jsonSerialize()
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 */
	public function jsonSerialize() {
		return json_encode($this->data);
	}

	/**
	 * method allows a class to decide how it will react when it is treated like
	 * a string.
	 * For example, what echo $obj
	 * @return string
	 * @link
	 *       http://www.php.net/manual/en/language.oop5.magic.php#object.tostring
	 */
	public function __toString() {
		return $this->jsonSerialize();
	}

	/**
	 * triggered by calling isset() or empty() on inaccessible properties.
	 * @param unknown $offset
	 * @link
	 *       http://www.php.net/manual/en/language.oop5.overloading.php#object.isset
	 */
	public function __isset($offset) {
		return $this->offsetExists($offset);
	}

	/**
	 * Get array value based on path
	 * @param array $data
	 * @param array $paths
	 * @return mixed
	 */
	private function getValue($data, array $paths) {
		// var_dump(is_object($data), is_array($data));
		if (! is_array($data) && ! is_object($data))
			return null;
		
		$temp = $data;
		foreach($paths as $ndx) {
			$ndx = trim($ndx); // remove whitespace
			if (is_object($temp)) {
				$temp = isset($temp->{$ndx}) ? $temp->{$ndx} : null;
			} else {
				$temp = isset($temp[$ndx]) ? $temp[$ndx] : null;
			}
		}
		return $temp;
	}

	/**
	 *
	 * @param array $data
	 * @param array $paths
	 * @param mixed $value
	 * @return NULL unknown
	 */
	private function putValue($data, array $paths, $value) {
		// var_dump(is_object($data), is_array($data));
		if (! is_array($data) && ! is_object($data))
			return null;
		
		$temp = &$data;
		$last = array_pop($paths); // get last element
		foreach($paths as $ndx) {
			$ndx = trim($ndx); // remove whitespace
			if (is_object($temp)) {
				$temp = &$temp->{$ndx};
			} else {
				$temp = &$temp[$ndx];
			}
		}
		
		// Check if auto append
		if ($last == "*" || $last == "$" || $last == "?") {
			if (is_object($temp)) {
				$var = "var";
				$i = 0;
				do {
					$i ++;
					$var = "var_$i";
				} while(isset($temp->{$var})); // generate key
				$temp->{$var} = $value;
				$last = $var;
			} else {
				array_push($temp, $value); // add to the end
				end($temp);
				$last = key($temp);
			}
		} else {
			
			if (is_object($temp)) {
				if (isset($temp->{$last}))
					throw new \ErrorException("Can't Inject Data $last already exists");
				$temp->{$last} = $value;
			} else {
				if (isset($temp[$last]))
					throw new \ErrorException("Can't Inject Data $last already exists");
				$temp[$last] = $value;
			}
		}
		
		return $last;
	}
}

?>