<?php
function config($name, $value = null) {
	if ($value == null) {
		return App\Contract\Config::get($name);
	} else {
		return App\Contract\Config::set($name, $value);
	}
}
?>
