<?php
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
	public function setUp()
	{
		mkdir(dirname(__DIR__) . '/config');
		$str = "<?php return ['name' => 'Config']; ?>";
		file_put_contents(dirname(__DIR__) . '/config/app.php', $str);
	}
	public function testGetName()
	{
		$name = 'Config';
		
		$this->assertEquals($name, config('app.name'));
	}
	public function tearDown()
	{
		unlink(dirname(__DIR__) . '/config/app.php');
		rmdir(dirname(__DIR__) . '/config');
	}
}
?>