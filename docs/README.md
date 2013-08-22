Examples
=============

I simple wrapper `POST`, `GET` , `REQUEST` or any `Array` in PHP

####  Links

- [General Usage](USAGE_GENERAL.md)
- [Using Filters](USAGE_FILTER.md)
	
#### Basic
	
```PHP


include 'src/Varriable.class.php';
include 'src/filter/Parsable.class.php'; // Interface to allow you extend filter
include 'src/filter/Basic.class.php'; // Basic Filter you can create yours

use \super\filter\Basic;
use \super\Varriable;

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


//Start super Variable
$_POST = new Varriable($_POST);

// Ignore Some keys
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
 * This would return error because modification is disable by default 
 * public function __construct($data, $flags = Varriable::DISABLE_SET) 
 */
echo $_POST['hello'] = "Modify";

````

Output

	Hello word
	Hello word
	Hello word
	Hello word
	Hello word
	Let's meet 4:30am Ât the café 
	Fatal error: Uncaught exception 'ErrorException' with message 'Offset assignment disabled'

