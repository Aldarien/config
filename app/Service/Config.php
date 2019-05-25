<?php
namespace App\Service;

//use Config\Config as CModel;

class Config {
  protected $data;
  protected $folders;

  public function __construct($config_folder = null) {
    if ($config_folder == null) {
      $config_folder = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'config';
    }
    $this->data = (object) [];
    $this->addFolder($config_folder);
  }
  public function addFolder(string $folder) {
    if (!file_exists($folder)) {
      throw new \Exception($folder . ' folder does not exist.');
    }
    $this->folders []= $folder;
    $this->load($folder);
    return $this;
  }

  protected function load($folder) {
    $files = new \DirectoryIterator($folder);
    foreach ($files as $file) {
      if ($file->isDir()) {
        continue;
      }
      $loader = implode("\\", ["Aldarien", "FileLoaders", str_replace('YML', 'YAML', strtoupper($file->getExtension()) . 'Loader')]);
      if (!class_exists($loader)) {
        continue;
      }
      $loader = new $loader($folder . DIRECTORY_SEPARATOR . $file->getFilename());
      $this->data->{$loader->getName()} = $loader->load();
    }
  }
  /*
  Database implementation
  public function dbload($container) {
    $migrations = (new Migrator($container))->load($this->get('locations.configs.migrations'))->check()->migrate();
    $configs = $container->model->find(CModel::class)->many();
    if (!$configs) {
      return $this;
    }
    $configuration = [];
    foreach ($configs as $config) {
      $name = $config->denomination;
      $configuration[$name] = $config->value;
    }
    $this->data->configuration = (object) $configuration;
  }*/
  public function get($name, $current = null, $full = '') {
    if ($current == null) {
      $current = $this->data;
      $full = $name;
    }
    if (is_array($current)) {
      $current = (object) $current;
    }
    $name = explode('.', $name);
    if (count($name) > 1) {
      $f = array_shift($name);
      if (!isset($current->$f)) {
        throw new \InvalidArgumentException($f . ' not found.');
      }
      return $this->get(implode('.', $name), $current->$f, $full);
    }
    if (!isset($current->{$name[0]})) {
      return null;
    }
    $output = $current->{$name[0]};
    $clean = $this->clean($output);
    if ($clean != $output) {
      $this->set($full, $clean);
    }
    return $clean;
  }
  public function set($name, $value) {
    $name = "->" . implode("->", explode('.', $name));
    $str = "\$this->data{$name} = \$value;";
    eval($str);
  }
  protected function clean($value) {
    if (!is_string($value)) {
      return $value;
    }
    if (strpos($value, '{') !== false) {
      $output = [];
      $str = "/\{(.*)\}/";
      preg_match_all($str, $value, $output, \PREG_SET_ORDER);
      foreach ($output as $d) {
        $value = str_replace($d[0], $this->get($d[1]), $value);
      }
    }
    if (strpos($value, '(') !== false) {
      $str = "\$output = " . $value . ';';
      eval($str);
      $value = $output;
    }
    return $value;
  }
}
