<?php
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
	public function testGetName()
	{
		$name = 'Config';
		
		$this->assertEquals($name, config('app.name'));
	}
}
?>