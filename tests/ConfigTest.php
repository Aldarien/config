<?php
use App\Alias\Test;

class ConfigTest extends Test
{
	public function testGetNamePhp()
	{
		$name = 'Config';

		$this->assertEquals($name, config('app.name'));
	}
	public function testGetNameJson()
	{
		$name = 'Config';

		$this->assertEquals($name, config('json.name'));
	}
	public function testGetNameYaml()
	{
		$name = 'Config';

		$this->assertEquals($name, config('yaml.name'));
	}
	public function testSetNamehp()
	{
		$new_name = 'Config_Test';
		config('app.name', $new_name);
		$this->assertEquals($new_name, config('app.name'));
	}
	public function testSetNameJson()
	{
		$new_name = 'Config_Test';
		config('json.name', $new_name);
		$this->assertEquals($new_name, config('json.name'));
	}
	public function testSetNameYaml()
	{
		$new_name = 'Config_Test';
		config('yaml.name', $new_name);
		$this->assertEquals($new_name, config('yaml.name'));
	}
	public function testArrayGetPhp()
	{
		$this->assertArrayHasKey('data1', config('app.test_array'));
	}
	public function testArrayGetJson()
	{
		$this->assertArrayHasKey('data1', config('json.test_array'));
	}
	public function testArrayGetYaml()
	{
		$this->assertArrayHasKey('data1', config('yaml.test_array'));
	}
	public function testSameSectionName()
	{
		$this->assertEquals('Config', config('yaml.last_name'));
	}
	public function testDuplicateValue()
	{
		config('json.name', 'Config2');
		$this->assertEquals('Config2', config('json.name'));
	}
	public function testAddFile()
	{
		$filename = dirname(__DIR__) . '/composer.json';
		App\Contract\Config::addFile($filename);
		$this->assertEquals('aldarien/config', config('composer.name'));
	}
}
?>
