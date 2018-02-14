<?php

namespace Areus;

class Router extends \Areus\ApplicationModule {
	private $routes = [];
	private $filters = [];
	private $group = null;

	public function filter($key, $func) {
		$this->filters[$key] = $func;
	}

	public function group($options, $function) {
		if(isset($options['prefix'])) $options['prefix'] = $this->trim($options['prefix']);

		$this->group = $options;
		$function();
		$this->group = null;
	}

	public function get($route, $options) {
		if(!is_array($options))	$options = ['uses' => $options];
		$options['method'] = 'get';
		$this->any($route, $options);
	}

	public function post($route, $options) {
		if(!is_array($options))	$options = ['uses' => $options];
		$options['method'] = 'post';
		$this->any($route, $options);
	}

	public function put($route, $options) {
		if(!is_array($options))	$options = ['uses' => $options];
		$options['method'] = 'put';
		$this->any($route, $options);
	}

	public function patch($route, $options) {
		if(!is_array($options))	$options = ['uses' => $options];
		$options['method'] = 'patch';
		$this->any($route, $options);
	}

	public function delete($route, $options) {
		if(!is_array($options))	$options = ['uses' => $options];
		$options['method'] = 'delete';
		$this->any($route, $options);
	}

	public function any($route, $options) {
		$route = $this->trim($route);

		if($this->group != null && isset($this->group['prefix']))
			$route = $this->trim($this->group['prefix']).'/'.$route;

		if(!is_array($options)) {
			$options = ['uses' => $options];
		}
		if($this->group != null) {
			$options = array_merge($this->group, $options);
		}

		if(isset($options['as']) && isset($this->routes[$options['as']])) {
			throw new \RuntimeException('Route with name ' .$options['as']. ' is already registered.');
		}

		$options['pattern'] = '/'.$route;

		if(isset($options['as']))
			$this->routes[$options['as']] = $options;
		else
			$this->routes[] = $options;
	}

	private function compile($route) {
		$pattern = $route['pattern'];

		return '#^'.preg_replace_callback('/\{(\w+)\}/', function($match) use ($route) {
			$regex = '[\w-]+';
			if(isset($route['validation']) && isset($route['validation'][$match[1]]))
				$regex = $route['validation'][$match[1]];
			return '(?P<'.$match[1].'>'.$regex.')';
		}, $pattern).'$#';
	}

	private function trim($url) {
		return trim($url, '/');
	}

	private function callFilter($filter) {
		$continue = true;

		// handle multiple filters
		if(is_array($filter)) {
			foreach($filter as $f) {
				$continue = $continue && $this->callFilter($f);
			}
			return $continue;
		}

		// execute single filter
		if(!isset($this->filters[$filter])) {
			throw new \RuntimeException('Could not find filter: '.$filter);
		}
		$filter = $this->filters[$filter];

		$reflection = new \ReflectionFunction($filter);
		$filterResult = call_user_func_array($filter, $this->prepareArguments($reflection));

		if($filterResult !== null && $filterResult !== true) {
			$continue = false;
		}
		return $continue;
	}

	private function callRoute($route, $args = []) {
		//check filters
		$abort = false;
		if(isset($route['before'])) {
			if(!$this->callFilter($route['before'])) {
				$abort = true;
			}
		}

		//finally call the route
		if(!$abort) {
			$uses = $route['uses'];
			$reflection = null;

			if(is_callable($uses)) {
				$reflection = new \ReflectionFunction($uses);
				call_user_func_array($uses, $this->prepareArguments($reflection, $args));
			} else if(is_string($uses)) {
				$act = explode('@', $uses);
				if(count($act) == 1) {
					$uses = $act;
					$reflection = new \ReflectionFunction($uses);
					call_user_func_array($uses, $this->prepareArguments($reflection, $args));
				} else if(count($act) == 2) {
					$ctrl = $this->app->make($act[0]);
					$reflection = new \ReflectionMethod($ctrl, $act[1]);
					call_user_func_array([$ctrl, $act[1]], $this->prepareArguments($reflection, $args));
				} else {
					throw new \RuntimeException('Could not parse route controller: '.$uses);
				}
			}
		}
	}

	private function prepareArguments($reflection, $args = []) {
		$fargs = [];
		foreach($reflection->getParameters() as $param) {
			$key = $param->getName();
			if(isset($args[$key])) {
				$fargs[$key] = $args[$key];
			}
			else if($param->getClass() !== null) {
				$bind = $this->app->make($param->getClass()->getName());
				if($bind !== null) {
					$fargs[$key] = $bind;
				}
			}
			else if($param->isDefaultValueAvailable()) {
				$fargs[$key] = $param->getDefaultValue();
			}
		}
		return $fargs;
	}

	public function call404() {
		if(isset($this->routes['404']))
			$this->callRoute($this->routes['404']);
		else
			throw new \RuntimeException('No action found for request: 404');
	}

	public function run($path = null) {
		if($path == null) {
			if(isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])) {
				$path = $_SERVER['PATH_INFO'];
			} else {
				$path = explode('?', $_SERVER['REQUEST_URI'])[0];
			}
		}
		$path = '/'.trim($path, '/');

		$missing = true;
		foreach($this->routes as $id => $route) {
			$pattern = $route['pattern'];
			if(isset($route['method']) && $route['method'] != strtolower($_SERVER['REQUEST_METHOD'])) {
				continue;
			}
			if($path == $pattern) {
				$this->callRoute($route);
				$missing = false;
				break;
			} else if(preg_match($this->compile($route), $path, $matches) === 1) {
				$this->callRoute($route, $matches);
				$missing = false;
				break;
			}
		}

		if($missing) {
			$this->call404();
		}
	}

	public function dump() {
		$ret = '<pre>';
		foreach($this->routes as $id => $route) {
			$ret .= 'ANY '.$route['pattern']."\n";
			$ret .= $this->dumpArray($route, "\t");
		}
		$ret .= '</pre>';
		return $ret;
	}

	private function dumpArray($arr, $pre) {
		$ret = "";
		foreach($arr as $k => $v) {
			if($k == 'uses' && !is_string($v))
				$v = '[Closure]';
			if(is_array($v)) {
				$ret .= $pre.$k.' => [Array]'."\n";
				$ret .= $this->dumpArray($v, $pre."\t");
			} else
				$ret .= $pre.$k.' => '.$v."\n";
		}
		return $ret;
	}
}
