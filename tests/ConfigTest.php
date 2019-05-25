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
    $data = "<?php return ['name' => 'Config', 'test_array' => ['data1' => 1, 'data2' => 2]]; ?>";
    $this->createFile($filename, $data);

    $filename = implode(DIRECTORY_SEPARATOR, [$folder, 'json.json']);
    $arr = ['name' => 'Config', 'test_array' => ['data1' => 1, 'data2' => 2]];
    $data = json_encode($arr);
    $this->createFile($filename, $data);

    $filename = implode(DIRECTORY_SEPARATOR, [$folder, 'yaml.yml']);
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
    $expected = 'Config';
    $real = $service->get('app.name');
    $this->assertEquals($real, $expected);
  }
}
