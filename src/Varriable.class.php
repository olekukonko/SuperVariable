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
namespace super;

use super\filter\Parsable;

class Varriable implements \ArrayAccess, \IteratorAggregate, \JsonSerializable {
	const ALLOW_SET = 1; // Allow you to set varraible (Disabled by Default)
	const ALLOW_GET = 2; // Allow you to get varriable (Enabled By Default)
	                     
	// Data Storage
	private $data = array();
	private $flags;
	private $filter = null;
	private $offsetFilter = array();

	/**
	 *
	 * @param array $array
	 * @param Parsable $filter
	 * @param int $flags
	 */
	public function __construct(array $array, Parsable $filter = null, $flags = Varriable::ALLOW_GET) {
		$this->data = $array;
		$this->flags = $flags;
		$this->filter = $filter;
	}

	/**
	 * Modify Varriable Sata
	 * @param array $array
	 */
	public function setData(array $array) {
		$this->data = $array;
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
			var_dump($data);
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
		$path = explode(".", $path);
		if ($var = $this->offsetGet(array_shift($path))) {
			return $this->getValue($path, $var);
		}
		return $var;
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
		if ($this->flags ^ Varriable::ALLOW_GET)
			throw new \ErrorException("Offset retrival disabled");
			// Fild filter to use
		$filter = isset($this->offsetFilter[$offset]) ? $this->offsetFilter[$offset] : $this->filter;
		// Illegal string-offset Fix
		return $this->offsetExists($offset) ? ($filter ? $filter->parse($this->data[$offset]) : $this->data[$offset]) : null;
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

	private function getValue(array $paths, array $data) {
		$temp = $data;
		foreach ( $paths as $ndx ) {
			$temp = isset($temp[$ndx]) ? $temp[$ndx] : null;
		}
		return $temp;
	}
}

?>