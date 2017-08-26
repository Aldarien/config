<?php
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
	public function setUp()
	{
		mkdir(dirname(__DIR__) . '/config');
		$str = "<?php return ['name' => 'Config', 'test_array' => ['data1' => 1, 'data2' => 2]]; ?>";
		file_put_contents(dirname(__DIR__) . '/config/app.php', $str);
	}
	public function testGetName()
	{
		$name = 'Config';
		
		$this->assertEquals($name, config('app.name'));
	}
	public function testSetName()
	{
		$new_name = 'Config_Test';
		config('app.name', $new_name);
		$this->assertEquals($new_name, config('app.name'));
	}
	public function testArrayGet()
	{
		$this->assertArrayHasKey('data1', config('app.test_array'));
	}
	public function tearDown()
	{
		unlink(dirname(__DIR__) . '/config/app.php');
		rmdir(dirname(__DIR__) . '/config');
	}
}
?>