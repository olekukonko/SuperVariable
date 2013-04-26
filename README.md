SuperVariable
=============

I simple wrapper POST, GET and REQUEST in PHP

#### Config 

```PHP
	
include 'src/Varriable.class.php';
include 'src/filter/Parsable.class.php'; // Interface to allow you extend filter
include 'src/filter/Basic.class.php'; // Basic Filter you can create yours

use \super\filter\Parsable;
use \super\filter\Basic;

use \super\Varriable;


$var = array();
$var["name"] = "<b>" . $_SERVER['SERVER_NAME'] . "</b>";
$var["example"]['xss'] = '<IMG SRC=javascript:alert("XSS")>';
$var["example"]['sql'] = "x' AND email IS NULL; --";
$var["example"]['filter'] = "Let's meet  4:30am Ât the \tcafé\n";


//Set fake post data
$_POST['testing'] = $var;
$_POST['selected'] = "ÂÂÂÂÂÂÂÂÂÂÂÂÂÂÂHello WorldÂÂÂÂÂÂÂÂÂÂÂÂÂÂÂÂÂ";
$_POST['phone'] = "Phone+888(008)9903";
$_POST['hello'] = "Hello word";

```


#### Example 1

```PHP
$_POST = new Varriable($_POST);
echo $_POST("hello"), PHP_EOL;
echo $_POST->hello, PHP_EOL;
echo $_POST['hello'], PHP_EOL;
echo $_POST->hello(), PHP_EOL;
echo $_POST->offsetGet("hello");
```

#### Output 

It would all give you the same value 

	Hello word
	Hello word
	Hello word
	Hello word
	Hello word
	
	
#### Example 2

This class automatically handles Invalid Offset error


```PHP
// Before
$var = isset($_POST['var']) ? $_POST['var'] : null;

// now
$var = $_POST['var']; // It check for empty values automatically
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




	
	
