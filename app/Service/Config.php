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
		//$files = glob($this->dir . '/*.{php,json,yml}', GLOB_BRACE);
		$files = $this->getFiles($this->dir);
		foreach ($files as $file) {
			$name = $file->name;
			$d = $this->getData($file);
			$data[$name] = $d;
			$data = array_merge($data, $this->translateArray($d, $name));
			foreach ($data as $key => $value) {
				$this->add($key, $value);
			}
		}
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
		$d = $this->getData($file);
		$data[$name] = $d;
		$data = array_merge($data, $this->translateArray($d, $name));
		foreach ($data as $key => $value) {
			$this->add($key, $value);
		}
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
	protected function translateArray($array, $level)
	{
		$output = [];
		foreach ($array as $k1 => $l1) {
			$key = $level . '.' . $k1;
			if (is_array($l1)) {
				$output[$key] = $l1;
				$output = array_merge($output, $this->translateArray($l1, $key));
			} else {
				$output[$key] = $l1;
			}
		}
		return $output;
	}
	protected function add($field, $value)
	{
		if (isset($this->data[$field])) {
			if ($this->data[$field] == $value) {
				return;
			}
			if (is_array($this->data[$field])) {
				$this->data[$field] = $this->merge($this->data[$field], $this->replace($value));
			} else {
				$this->data[$field] = $this->replace($value);
			}
		} else {
			$this->data[$field] = $this->replace($value);
		}
	}
	protected function merge($arr1, $arr2)
	{
		$output = $arr1;
		foreach ($arr2 as $k => $value) {
			if (isset($arr1[$k])) {
				if ($arr1[$k] == $value) {
					continue;
				}
				if (is_array($arr1[$k])) {
					$output[$k] = $this->merge($arr1[$k], $value);
				} else {
					$output[$k] = array_merge([$arr1[$k]], $value);
				}
			} else {
				$output[$k] = $value;
			}
		}
		return $output;
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
				$value = str_replace('{' . $rep . '}', $this->get($rep), $value);
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
	public function set($name, $value)
	{
		$this->add($name, $value);
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
