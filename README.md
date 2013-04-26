SuperVariable
=============

I simple wrapper `POST`, `GET` , `REQUEST` or any `Array` in PHP

#### Config 

```PHP
include 'src/Varriable.class.php';
include 'src/filter/Parsable.class.php'; // Interface to allow you extend filter
include 'src/filter/Basic.class.php'; // Basic Filter you can create yours

use \super\filter\Basic;
use \super\Varriable;


// Generate Fake post Data
$_POST = array();
$_POST['testing']["name"] = "<b>" . $_SERVER['SERVER_NAME'] . "</b>";
$_POST['testing']["example"]['xss'] = '<IMG SRC=javascript:alert("XSS")>';
$_POST['testing']["example"]['sql'] = "x' AND email IS NULL; --";
$_POST['testing']["example"]['filter'] = "Let's meet  4:30am Ât the \tcafé\n";
$_POST['selected'] = "ÂÂÂÂÂÂÂÂÂÂÂÂÂÂÂHello WorldÂÂÂÂÂÂÂÂÂÂÂÂÂÂÂÂÂ";
$_POST['phone'] = "Phone+888(008)9903";
$_POST['hello'] = "Hello word";
$_POST['image'] = file_get_contents("http://i.imgur.com/YRz0AI7.png");
$_POST['binary'] = mcrypt_create_iv(10, MCRYPT_DEV_URANDOM);
```


#### Example 1

```PHP
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
	¯_‰”ÝMºÎâ
	Fatal error: Uncaught exception 'ErrorException' with message 'Offset assignment disabled'
	
	
#### Example 2

This class automatically handles Invalid Offset error


```PHP
// Before
$var = isset($_POST['var']) ? $_POST['var'] : null;

// now
$var = $_POST['var']; // No need to check
```


#### Example 3

Supports Filters 

```PHP
$_POST = new Varriable($_POST, new Basic(Basic::FILTER_XSS));
print_r($_POST['example']);
```

Output 

	Array
	(
	    [0] => &lt;b&gt;localhost&lt;/b&gt;
	    [1] => Array
	        (
	            [0] => &lt;IMG SRC=javascript:alert(&quot;XSS&quot;)&gt;
	            [1] => x&#039; AND email IS NULL; --
	            [2] => 
	        )
	
	)



####  Example 4

You can limit the filter to specific offset


```PHP
//Before 
echo $_POST['phone'];

//Convert post to super
$_POST = new Varriable($_POST);
$_POST->offsetFilter("phone", new Basic(Basic::FILTER_INT));

//After 
echo $_POST->phone;
```

Output 


	Phone+888(008)9903  //before
	+8880089903         //after 
	
	
####  Example 4

Loops

```PHP
// Loop normally
foreach ( $_POST as $v ) {
	print_r($v);
}

// or Recursively
foreach (new RecursiveIteratorIterator($_POST->getRecursiveIterator()) as $k => $v ) {
	echo $v, PHP_EOL;
}
```



####  Example 5

Enable SET and Disable GET

```PHP
$_POST = new Varriable($_POST, null, Varriable::ALLOW_SET);
$_POST['hello'] = "World";

echo $_POST['hello']; // will return error;
                       
// you can only loop or convert the iterator to array
foreach ( $_POST as $v ) {
	print_r($v);
}
```



####  Example 6

Another simple type of filter is Callback 

```PHP
$callback = new Callback();

// Add callback to keys when found
$callback->add("hello", function ($value, $key) {
	return strtoupper($value);
});

// You can also use regex with match
$callback->match("/^hello/", function ($value, $key) {
	return strtoupper($value);
});

$_POST = new Varriable($_POST, $callback);
echo $_POST['hello'];
```

Output 

	HELLO
	
	
	

#### Licence
##### *** Please Note that this is still exprimental

	Copyright 2012 Oleku Konko
	
	Licensed under the Apache License, Version 2.0 (the "License");
	you may not use this file except in compliance with the License.
	You may obtain a copy of the License at
	
	http://www.apache.org/licenses/LICENSE-2.0
	
	Unless required by applicable law or agreed to in writing, software
	distributed under the License is distributed on an "AS IS" BASIS,
	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	See the License for the specific language governing permissions and
	limitations under the License.
	 




	
	
