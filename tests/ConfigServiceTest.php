<?php
use App\Alias\Test;
use App\Service\Config;

class ConfigServiceTest extends Test
{
  public function testLoadFiles()
  {
    $config = new Config();
    $output = 'Config';
    $this->assertEquals($output, $config->get('app.name'));
  }
  public function testGet()
  {
    $config = new Config();
    $output = 'Config';
    $this->assertEquals($output, $config->get('app.name'));
  }
}
