<?php
namespace Aldarien\FileLoaders;

use App\Alias\FileLoader;

class JSONLoader extends FileLoader {
  public function load() {
    $data = json_decode(trim(file_get_contents($this->filename)));
    return $this->arrayToObject($data);
  }
}
