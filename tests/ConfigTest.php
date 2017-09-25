<?php
use PHPUnit\Framework\TestCase;
use App\Contract\YamlWrapper;

class ConfigTest extends TestCase
{
	public function setUp()
	{
		mkdir(dirname(__DIR__) . '/config');
		$str = "<?php return ['name' => 'Config', 'test_array' => ['data1' => 1, 'data2' => 2]]; ?>";
		file_put_contents(dirname(__DIR__) . '/config/app.php', $str);
		$data = ['name' => 'Config', 'test_array' => ['data1' => 1, 'data2' => 2]];
		file_put_contents(dirname(__DIR__) . '/config/json.json', json_encode($data));
		file_put_contents(dirname(__DIR__) . '/config/yaml.yml', YamlWrapper::dump($data));
		$data = ['last_name' => 'Config'];
		file_put_contents(dirname(__DIR__) . '/config/yaml.json', json_encode($data));
	}
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
	public function tearDown()
	{
		unlink(dirname(__DIR__) . '/config/app.php');
		unlink(dirname(__DIR__) . '/config/json.json');
		unlink(dirname(__DIR__) . '/config/yaml.yml');
		unlink(dirname(__DIR__) . '/config/yaml.json');
		rmdir(dirname(__DIR__) . '/config');
	}
}
?>