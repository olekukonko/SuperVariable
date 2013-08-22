SuperVariable
=============

I simple wrapper `POST`, `GET` , `REQUEST` or any `Array` in PHP

#### Example 

```PHP
$array = array(
		"foo" => "bar",
		array(
				"name" => "super"
		)
);

// Start super Variable
$array = new Varriable($array);
// Array
echo $array['foo'] ; // returns bar
// Invoke
echo $array("foo"); // returns bar
// Object
echo $array->foo ; // returns bar
// Method
echo $array->foo() ; // returns bar
// Get via offsetGet
echo $array->offsetGet("hello");// returns bar

// Can can also find for sub arrays or Object
echo $array->find("foo.name"); // returns Super
```	


#### More Examples
- [Full Readme](docs/README.md)
- [General Usage](docs/USAGE_GENERAL.md)
- [Using Filters](docs/USAGE_FILTER.md)

#### Licence [MIT](http://opensource.org/licenses/MIT)

	Copyright (c) 2013 Oleku Konko
	
	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:
	
	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.
	
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
	 




	
	
