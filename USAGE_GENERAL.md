General Usage
=============

- [Set Default value](#DEFAULT)
- [Loops](#LOOPS)
- [Disable Modification](#MODIFICATION)
- [Find & INJECT](#FIND)


#### Fake Data 

```PHP
// Generate Fake post Data

$object = new stdClass();
$object->name = "delete";
$object->data = (object) array("age"=>21,"bad"=>"<b>Bad</b>");

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
$_POST['object'] = $object ;

```


	
<h2 id="DEFAULT">Set Default value</h2>
Set default values when variable is not found

```PHP
$_POST = new Varriable($_POST);

// This would have generated Invalid Offset error if not set but this system
// set default t= null automatically

var_dump($_POST['var']); // returns null

// You can set default values too
$_POST->setDefault(false) // change default from null to false
var_dump($_POST['var']); // returns false


$_POST->setDefault(array()) // change default from false to array
var_dump($_POST['var']); // returns array()
```


<h2 id="LOOPS">Loops</h2>

The Class Implements `IteratorAggregate` which is `Traversable`

```PHP
// Supports normal loop
foreach ( $_POST as $v ) {
	print_r($v);
}

// or Recursively
foreach (new RecursiveIteratorIterator($_POST->getRecursiveIterator()) as $k => $v ) {
	echo $v, PHP_EOL;
}
```



<h2 id="MODIFICATION">Modification</h2>

You put restriction on Modification by specifiing if variables age be `GET` or `SET` 
The `SET is disabled by default`

```PHP
$_POST = new Varriable($_POST, null);
$_POST['hello'] = "World"; // will return because you attempt to modify

// Allow both SET AND GET only GET IS allowed by default
$_POST = new Varriable($_POST, null,Varriable::ALLOW_SET_GET);
$_POST['hello'] = "World"; // OK
echo $_POST['hello']; // OK
                       
// you can only loop or convert the iterator to array
foreach ( $_POST as $v ) {
	print_r($v);
}

```

	
	
<h2 id="FIND">Find & Inject</h2>
You can easly find or inject elements at any position

```PHP

$_POST = new Varriable($_POST, new Basic(Basic::FILTER_XSS));

//Find any elements
echo $_POST->find("object.data.bad"), PHP_EOL; 

//Inject any elements
$_POST->inject("object.data.*", "<span onClick=\"javascript.alert('XSS Test')\" >Test</span>"); // add test to data
$_POST->inject("object.data.range",range(1,3)); //add range to data


print_r($_POST['object']);
```
Output 

	&lt;b&gt;Bad&lt;/b&gt  // Find Output
	
	stdClass Object // print_r object
	(
	    [name] => delete
	    [data] => stdClass Object
	        (
	            [age] => 21
	            [bad] => &lt;b&gt;Bad&lt;/b&gt;
	            [var_1] => &lt;span onClick=&quot;javascript.alert(&#039;XSS Test&#039;)&quot; &gt;Test&lt;/span&gt;     <--- Innjected
	            [range] => Array    <--- Injected
	                (
	                    [0] => 1
	                    [1] => 2
	                    [2] => 3
	                )
	
	        )
	
	)

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
	 




	
	
