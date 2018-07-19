<?php
namespace App\Alias;

use PHPUnit\Framework\TestCase;
use App\Contract\YamlWrapper;

class Test extends Testcase
{
  public static function setUpBeforeClass()
	{
    $root = dirname(dirname(__DIR__));
    mkdir($root . '/config');
		$str = "<?php return ['name' => 'Config', 'test_array' => ['data1' => 1, 'data2' => 2]]; ?>";
		file_put_contents($root . '/config/app.php', $str);
		$data = ['name' => 'Config', 'test_array' => ['data1' => 1, 'data2' => 2]];
		file_put_contents($root . '/config/json.json', json_encode($data));
		file_put_contents($root . '/config/yaml.yml', YamlWrapper::dump($data));
		$data = ['last_name' => 'Config'];
		file_put_contents($root . '/config/yaml.json', json_encode($data));
	}
  public static function tearDownAfterClass()
	{
    $root = dirname(dirname(__DIR__));
    unlink($root . '/config/app.php');
		unlink($root . '/config/json.json');
		unlink($root . '/config/yaml.yml');
		unlink($root . '/config/yaml.json');
		rmdir($root . '/config');
	}
}
