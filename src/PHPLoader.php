<?php
namespace Aldarien\FileLoaders;

use App\Alias\FileLoader;

class PHPLoader extends FileLoader {
  public function load() {
    $data = include_once($this->filename);
    return $this->arrayToObject($data);
  }
}
