<?php
include 'vendor/autoload.php';
use Oleku\SuperVarriable\Varriable;
class VarriableTest extends PHPUnit_Framework_TestCase {
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
	public function testBasic() {
		$var = new Varriable($this->getBasicData());
		
		$this->assertEquals("bar", $var ['foo']);
		$this->assertEquals("bar", $var('foo'));
		$this->assertEquals("bar", $var->foo);
		$this->assertEquals("bar", $var->foo());
		
		$this->assertEquals("super", $var ['foo'] ['name']);
		$this->assertEquals("super", $var->foo()->name);
		$this->assertEquals("super", $var->foo ['name']);
	}
	public function testBasicArray() {
		$var = new Varriable($this->getBasicData());
		
		$fish = array(
			"water" => array(
				"cute" 
			) 
		);
		$this->assertEquals($fish, $var ['fish']);
		$this->assertEquals($fish, $var('fish'));
		$this->assertEquals($fish, $var->fish);
		$this->assertEquals($fish, $var->fish());
	}
	public function testBasicInjection() {
		$var = new Varriable($this->getBasicData());
		$this->assertNotEmpty($var->inject("name.value", "injected"));
		$this->assertEquals("injected", $var ['name'] ['value']);
		$this->assertEquals("injected", $var->name ['value']);
		
		$var->inject("name.letter.*", "A");
		$var->inject("name.letter.*", "B");
		$var->inject("name.letter.*", "C");
		$var->inject("name.letter.*", "D");
		
		$this->assertEquals(array(
			"A",
			"B",
			"C",
			"D" 
		), $var ['name'] ['letter']);
	}
	public function testBasicFind() {
		$var = new Varriable($this->getBasicData());
		$var->find("fish.water");
		
		$this->assertEquals(array(
			"cute" 
		), $var->find("fish.water"));
		
		$this->assertEquals($var->find("0"), $var ['0']);
		$this->assertEquals($var->find("fish.water"), $var ['fish'] ['water']);
		$this->assertEquals($var->find("fish.water"), $var->fish ['water']);
		$this->assertEquals($var->find("fish.water"), $var->fish("water"));
		$this->assertEquals("cute", $var->fish("water.0"));
		$this->assertEquals("cute", $var->find("fish.water.0"));
		$this->assertEquals($var->find("fish.water.0"), $var->fish("water.0"));
	}
	public function testBasicAccess() {
		$var = new Varriable($this->getBasicData(), Varriable::DISABLE_ALL);
		$var->setException(false);
		
		$this->assertEquals(null, $var ['foo']);
		$this->assertEquals(null, $var('foo'));
		$this->assertEquals(null, $var->foo);
		$this->assertEquals(null, $var->foo());
		
		$var = new Varriable($this->getBasicData(), Varriable::DISABLE_SET);
		$var->setException(false);
		
		$this->assertEquals("bar", $var ['foo']);
		$var ['foo'] = "test";
		$this->assertEquals("bar", $var ['foo']);
		
		$var = new Varriable($this->getBasicData(), Varriable::DISABLE_GET);
		$var->setException(false);
		$var ['foo'] = "test";
		$this->assertEquals(null, $var ['foo']);
		
		$data = $var->getData();
		$this->assertEquals("test", $data ['foo']);
	}
}

?>