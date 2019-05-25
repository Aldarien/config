<?php
namespace Aldarien\FileLoaders;

use \Spyc;
use App\Alias\FileLoader;

class YAMLLoader extends FileLoader {
  public function load() {
    $data = Spyc::YAMLLoad($this->filename);
    $this->arrayToObject($data);
    return $data;
  }
}
