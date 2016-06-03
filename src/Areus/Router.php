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
		$this->any($route, $options);
	}

	public function post($route, $options) {
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

	private function callRoute($route, $args = []) {
		//check filters
		$abort = false;
		if(isset($route['before'])) {
			if(!isset($this->filters[$route['before']]))
				throw new \RuntimeException('Could not find filter: '.$route['before']);
			$filter = $this->filters[$route['before']];
			$reflection = new \ReflectionFunction($filter);
			$filterResult = call_user_func_array($filter, $this->prepareArguments($reflection, $args));

			if($filterResult !== null && $filterResult !== true) {
				$abort = true;
			}
			unset($filterResult);
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

	private function prepareArguments($reflection, $args) {
		$fargs = [];
		foreach($reflection->getParameters() as $param) {
			$key = $param->getName();
			if(isset($args[$key]))
				$fargs[$key] = $args[$key];
			else if($key == 'app')
				$fargs[$key] = $this->app;
			else if($key == 'req')
				$fargs[$key] = $this->app->req;
			else if($key == 'res')
				$fargs[$key] = $this->app->res;
			else if($param->isDefaultValueAvailable())
				$fargs[$key] = $param->getDefaultValue();
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
			$path = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : '';
			$path = '/'.trim($path, '/');
		}

		$missing = true;
		foreach($this->routes as $id => $route) {
			$pattern = $route['pattern'];
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

		if($missing)
			$this->call404();
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
