<?php
include 'vendor/autoload.php';

use Oleku\SuperVarriable\Varriable;
use Oleku\SuperVarriable\Filter\Basic;
class BasicFilterTest extends PHPUnit_Framework_TestCase {
	public function getBasicData() {
		return $array = array(
			"foo" => "bar",
			array(
				"name" => "super" 
			),
			
			"fish" => array(
				"water" => array(
					"cute" 
				) 
			) 
		);
	}
	public function getComplexData() {
		$object = new stdClass();
		$object->name = "delete";
		$object->data = ( object ) array(
			"age" => 21,
			"bad" => "<b>Bad</b>" 
		);
		
		$_POST = array();
		$_POST ['testing'] ["name"] = "<b>" . $_SERVER ['SERVER_NAME'] . "</b>";
		$_POST ['testing'] ["example"] ['xss'] = '<IMG SRC=javascript:alert("XSS")>';
		$_POST ['testing'] ["example"] ['sql'] = "x' AND email IS NULL; --";
		$_POST ['testing'] ["example"] ['filter'] = "Let's meet  4:30am Ât the \tcafé\n";
		$_POST ['selected'] = "ÂÂÂÂÂÂÂÂÂÂÂÂÂÂÂHello WorldÂÂÂÂÂÂÂÂÂÂÂÂÂÂÂÂÂ";
		$_POST ['phone'] = "Phone+888(008)9903";
		$_POST ['hello'] = "Hello word";
		$_POST ['image'] = file_get_contents("http://i.imgur.com/YRz0AI7.png");
		$_POST ['binary'] = mcrypt_create_iv(10, MCRYPT_DEV_URANDOM);
		$_POST ['object'] = $object;
		
		return $_POST;
	}
	public function testFilterKey() {
		$var = new Varriable($this->getBasicData());
		$var->setFilter(new Basic(Basic::FILTER_XSS));
		
		$this->assertEquals("bar", $var ['foo']);
		$this->assertEquals("cute", $var ['fish'] ['water'] [0]);
	}
}

?>