SuperVariable
=============

I simple wrapper `POST`, `GET` , `REQUEST` or any `Array` in PHP

#### Example 

```PHP
include 'src/Varriable.class.php';
include 'src/filter/Parsable.class.php'; // Interface to allow you extend filter
include 'src/filter/Basic.class.php'; // Basic Filter you can create yours

use \super\filter\Basic;
use \super\Varriable;


// Generate Fake post Data
$_POST['hello'] = "Hello word";



```PHP
//Start super Variable
$_POST = new Varriable($_POST);


/**
 * You can Ignore some fields that should not be filtred
 * eg Binary , XML , JSON etc;
 */
 
$_POST->ignore("image", "binary");

echo $_POST['hello'], PHP_EOL; // array
echo $_POST->hello, PHP_EOL; // object
echo $_POST("hello"), PHP_EOL; // function

echo $_POST->offsetGet("hello"), PHP_EOL; // direct
echo $_POST->hello(), PHP_EOL; // methods
                               
// Lest have more fun and file array based on path
echo $_POST->find("testing.example.filter"), PHP_EOL;

// This ould not be modified because of ignore
echo $_POST['binary'], PHP_EOL;

/*
 * This would return error because modification is disable but it can be enabled
 * $_POST = new Varriable($_POST,null,Varriable::ALLOW_GET |
 * Varriable::ALLOW_SET);
 */
echo $_POST['hello'] = "Modify";

```

#### Output 

It would all give you the same value 

	Hello word
	Hello word
	Hello word
	Hello word
	Hello word
	Let's meet 4:30am Ât the café	
	Fatal error: Uncaught exception 'ErrorException' with message 'Offset assignment disabled'
	


#### More Examples
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
	 




	
	
