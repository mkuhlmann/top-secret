<?php

namespace Areus;

class Config {
	protected $config;

	public function __construct(\Areus\Application $app) {
		$this->config = require $app->configPath().'/default.php';
		if(file_exists($app->configPath().'/local.php')) {
			$this->config = $this->mergeArrayDeep([$this->config, require $app->configPath().'/local.php']);
		}
	}

	public function asArray() {
		return $this->config;
	}

	public function __get($key) {
		return $this->get($key);
	}

	public function get($dot, $default = null) {
		$dot = explode('.', $dot);
		$cfg = $this->config;
		foreach($dot as $d) {
			if(isset($cfg[$d])) {
				$cfg =& $cfg[$d];
			} else {
				return $default;
			}
		}
		return $cfg;
	}
	private function mergeArrayDeep($arrays) {
		$result = array();
		foreach ($arrays as $array) {
			foreach ($array as $key => $value) {
				if (is_integer($key)) {
					$result[] = $value;
				}
				elseif (isset($result[$key]) && is_array($result[$key]) && is_array($value)) {
					$result[$key] = $this->mergeArrayDeep(array($result[$key], $value));
				}
				else {
					$result[$key] = $value;
				}
			}
		}
		return $result;
	}
}
