<?php
namespace App\Service;

use App\Contract\YamlWrapper;

class Config
{
	protected $dir;
	protected $data;

	public function __construct($dir = null)
	{
		if ($dir == null) {
			$dir = realpath(dirname(dirname(__DIR__)) . '/config') . DIRECTORY_SEPARATOR;
		}
		$this->dir = $dir;
		$this->load();
	}
	protected function load()
	{
		$files = $this->getFiles($this->dir);
		foreach ($files as $file) {
			$name = $file->name;
      $data = $this->getData($file);
      $this->set($name, $data);
		}
		$this->checkValues();
	}
	protected function getFiles($location)
  {
    $files = [];
    $d = new \DirectoryIterator($location) or die("getFileList: Failed opening directory $location for reading");
    foreach ($d as $fileinfo) {
      if ($fileinfo->isDot() or $fileinfo->isDir()) {
        continue;
      }
			if (!preg_match("/\.(php|json|yml)*$/i", $fileinfo->getFilename(), $matches)) {
			  continue;
			}
			$files []= (object) [
        'filename' => "{$location}{$fileinfo}",
        'name' => strtolower($fileinfo->getBasename('.' . $fileinfo->getExtension())),
        'type' => strtolower($fileinfo->getExtension())
      ];
    }
    return $files;
  }
	public function loadFile(string $filename)
	{
		if (!file_exists(realpath($filename))) {
			return false;
		}
		$info = pathinfo($filename);
		$file = (object) ['name' => $info['filename'], 'filename' => $filename, 'type' => strtolower($info['extension'])];
		$name = $file->name;
		$data = $this->getData($file);
    $this->add($name, $data);
		return true;
	}
	protected function getData($file)
	{
		switch ($file->type) {
			case 'php':
				return include $file->filename;
			case 'json':
				return json_decode(file_get_contents($file->filename), true);
			case 'yml':
				return YamlWrapper::load($file->filename);
			default:
				throw new \DomainException('Invalid file extension for ' . $file->filename);
		}
	}
	protected function set($name, $value)
  {
    $this->data[$name] = $value;
    if (is_array($value)) {
      foreach ($value as $key => $val) {
        $n = $name . '.' . $key;
        $this->add($n, $val);
      }
    }
  }
	public function add($field, $value)
	{
		$this->set($field, $value);
		$this->checkValues();
	}
	protected function checkValues($array = null)
  {
    if ($array == null) {
      foreach ($this->data as $key => $config) {
        $this->data[$key] = $this->checkValues($config);
      }
      return;
    }
    if (is_array($array)) {
      foreach ($array as $key => $val) {
        $array[$key] = $this->checkValues($val);
      }
      return $array;
    }
    return $this->replace($array);
  }
	protected function replace($value)
  {
    if (is_array($value)) {
			foreach ($value as $k => $v) {
				$value[$k] = $this->replace($v);
			}
			return $value;
		}
    if (strpos($value, '{') !== false) {
  		while(strpos($value, '{') !== false) {
  			$ini = strpos($value, '{') + 1;
  			$end = strpos($value, '}', $ini);
  			$rep = substr($value, $ini, $end - $ini);
        $get = $this->get($rep);
        if (strpos($get, '{') !== false) {
          $get = $this->replace($get);
        }
  			$value = str_replace('{' . $rep . '}', $get, $value);
  		}
    }
    return $value;
  }

	public function get($name = null)
	{
		if ($name == null) {
			return $this->data;
		}
		if (isset($this->data[$name])) {
			return $this->data[$name];
		}
		return null;
	}
	public function remove($name)
  {
    $keys = explode('.', $name);
    for ($i = 0; $i < count($keys); $i ++) {
      $n = implode('.', array_slice($keys, 0, $i + 1));
      $str = "unset(\$this->data['{$n}']";
      for ($j = $i + 1; $j < count($keys); $j ++) {
        $str .= "['{$keys[$j]}']";
      }
      $str .= ');';
      eval($str);
    }
  }
}
?>
