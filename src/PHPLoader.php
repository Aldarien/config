<?php
namespace Aldarien\FileLoaders;

use App\Alias\FileLoader;

class PHPLoader extends FileLoader {
  public function load() {
    return $this->arrayToObject(include_once($this->filename));
  }
}
