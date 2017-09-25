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
			$dir = realpath(root() . '/config');
		}
		$this->dir = $dir;
		$this->load();
	}
	protected function load()
	{
		$files = glob($this->dir . '/*.{php,json,yml}', GLOB_BRACE);
		foreach ($files as $file) {
			$info = pathinfo($file);
			$name = $info['filename'];

			$d = $this->getData($file);
			$data[$name] = $d;
			$data = array_merge($data, $this->translateArray($d, $name));
			foreach ($data as $key => $value) {
				$this->add($key, $value);
			}
		}
	}
	public function loadFile(string $filename)
	{
		if (!file_exists(realpath($filename))) {
			return false;
		}
		$info = pathinfo($filename);
		$name = $info['filename'];
		$d = $this->getData($filename);
		$data[$name] = $d;
		$data = array_merge($data, $this->translateArray($d, $name));
		foreach ($data as $key => $value) {
			$this->add($key, $value);
		}
		return true;
	}
	protected function getData($filename)
	{
		$info = pathinfo($filename);
		
		switch ($info['extension']) {
			case 'php':
				return include_once $filename;
			case 'json':
				return json_decode(file_get_contents($filename), true);
			case 'yml':
				return YamlWrapper::load($filename);
			default:
				throw new \DomainException('Invalid file extension for ' . $filename);
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
			if (is_array($this->data[$field])) {
				if (is_array($value)) {
					$this->data[$field] = array_merge($this->data[$field], $this->replace($value));
				} else {
					$this->data[$field] []= $this->replace($value);
				}
			} else {
				$this->data[$field] = $this->replace($value);
			}
		} else {
			$this->data[$field] = $this->replace($value);
		}
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
}
?>
