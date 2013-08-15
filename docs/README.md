Examples
=============

I simple wrapper `POST`, `GET` , `REQUEST` or any `Array` in PHP

####  Links

- [General Usage](USAGE_GENERAL.md)
- [Using Filters](USAGE_FILTER.md)
	
#### Basic
	
```PHP
//Start super Variable
$array = array("foo"=>"bar")
$array = new Varriable($array);
echo $array['foo'] ; // returns bar


//or
echo $array("foo"); // returns bar

//or
echo $array->foo ; // returns bar

//or
echo $array->foo() ; // returns bar

//or 
echo $array->find("foo"); // returns bar


````