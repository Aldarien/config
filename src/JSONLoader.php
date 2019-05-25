<?php
namespace Aldarien\FileLoaders;

use App\Alias\FileLoader;

class JSONLoader extends FileLoader {
  public function load() {
    return $this->arrayToObject(json_decode(trim(file_get_contents($this->filename))));
  }
}
