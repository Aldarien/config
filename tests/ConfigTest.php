<?php
use PHPUnit\Framework\TestCase;

use App\Service\Config;

class ConfigTest extends TestCase {
  public function setUp(): void {
    $root = dirname(__DIR__);
    $folder = implode(DIRECTORY_SEPARATOR, [$root, 'config']);
    if (!file_exists($folder)) {
      mkdir($folder);
    }

    $filename = implode(DIRECTORY_SEPARATOR, [$folder, 'app.php']);
    $data = "<?php return ['name' => 'App', 'test_array' => ['data1' => 1, 'data2' => 2]]; ?>";
    $this->createFile($filename, $data);

    $filename = implode(DIRECTORY_SEPARATOR, [$folder, 'json.json']);
    $arr = ['name' => 'JSON', 'test_array' => ['data1' => 1, 'data2' => 2]];
    $data = json_encode($arr);
    $this->createFile($filename, $data);

    $filename = implode(DIRECTORY_SEPARATOR, [$folder, 'yaml.yml']);
    $arr = ['name' => 'YAML', 'test_array' => ['data1' => 3, 'data2' => 4]];
    $data = Spyc::YAMLDump($arr);
    $this->createFile($filename, $data);

    $filename = implode(DIRECTORY_SEPARATOR, [$folder, 'yaml.json']);
    $data = json_encode(['last_name' => 'Config']);
    $this->createFile($filename, $data);
  }
  protected $files;
  protected function createFile(string $filename, string $data) {
    if (!file_exists($filename)) {
      $this->files []= $filename;
      file_put_contents($filename, $data);
    }
  }
  public function tearDown(): void {
    $root = dirname(__DIR__);
    $folder = implode(DIRECTORY_SEPARATOR, [$root, 'config']);
    foreach ($this->files as $filename) {
      if (file_exists($filename)) {
        unlink($filename);
      }
    }
    if (file_exists($folder)) {
      rmdir($folder);
    }
  }

  public function testLoad() {
    $folder = implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'config']);
    $service = new Config($folder);
    $this->assertTrue(true);

    return $service;
  }
  /**
   * @depends testLoad
   */
  public function testGetAppName(Config $service) {
    $expected = 'App';
    $real = $service->get('app.name');
    $this->assertEquals($expected, $real);

    return $service;
  }
  /**
   * @depends testGetAppName
   */
  public function testGetJsonName(Config $service) {
    $expected = 'JSON';
    $real = $service->get('json.name');
    $this->assertEquals($expected, $real);

    return $service;
  }
  /**
   * @depends testGetJsonName
   */
  public function testGetYamlName(Config $service) {
    $expected = 'YAML';
    $real = $service->get('yaml.name');
    $this->assertEquals($expected, $real);

    return $service;
  }
  /**
   * @depends testGetYamlName
   */
  public function testGetYamlLastName(Config $service) {
    $expected = 'Config';
    $real = $service->get('yaml.last_name');
    $this->assertEquals($expected, $real);

    return $service;
  }
}
