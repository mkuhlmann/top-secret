<?php

namespace Areus\Http;

use Laminas\Diactoros\Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Router extends \Areus\ApplicationModule {
	private $routes = [];
	private $filters = [];
	/** @var array */
	private $groupStack = [];
	private $groupStackMerged = [];

	private $request;

	public function filter($key, $func) {
		$this->filters[$key] = $func;
	}

	public function group($options, $function) : void {
		$options = $this->parseOptions($options);

		if(isset($options['prefix'])) $options['prefix'] = $this->trim($options['prefix']);
		
		array_push($this->groupStack, $options);
		$this->groupStackMerged = array_merge(...$this->groupStack);
		$function();
		array_pop($this->groupStack);

		if(count($this->groupStack) > 0)
			$this->groupStackMerged = array_merge(...$this->groupStack);
		else
			$this->groupStackMerged = [];
	}

	public function get($route, $options) {
		$options = $this->parseOptions($options);
		$options['method'] = 'get';
		$this->any($route, $options);
	}

	public function post($route, $options) {
		$options = $this->parseOptions($options);
		$options['method'] = 'post';
		$this->any($route, $options);
	}

	public function put($route, $options) {
		$options = $this->parseOptions($options);
		$options['method'] = 'put';
		$this->any($route, $options);
	}

	public function patch($route, $options) {
		$options = $this->parseOptions($options);
		$options['method'] = 'patch';
		$this->any($route, $options);
	}

	public function delete($route, $options) {
		$options = $this->parseOptions($options);
		$options['method'] = 'delete';
		$this->any($route, $options);
	}

	public function any($route, $options) {
		$route = $this->trim($route);
		$options = $this->parseOptions($options);

		if(isset($this->groupStackMerged['prefix']))
			$route = $this->trim($this->groupStackMerged['prefix']).'/'.$route;


		$options = array_merge($this->groupStackMerged, $options);
		$options['_group'] = count($this->groupStack);

		if(isset($options['as']) && isset($this->routes[$options['as']])) {
			throw new \RuntimeException('Route with name ' .$options['as']. ' is already registered.');
		}

		$options['pattern'] = '/'.$route;

		if(isset($options['as']))
			$this->routes[$options['as']] = $options;
		else
			$this->routes[] = $options;
	}

	/**
	 * 
	 * @param array|string $options
	 */
	private function parseOptions($options) : array {
		if(!is_array($options))	
			$options = ['uses' => $options];

		if(isset($options['before']) && !is_array($options['before'])) {
			$options['before'] = [ $options['before'] ];
		}

		return $options;
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

		// handle multiple filters
		if(is_array($filter)) {
			foreach($filter as $f) {
				$result = $this->callFilter($f);
				if($result !== null) { return $result; }
			}
			return null;
		}

		// execute single filter
		if(!isset($this->filters[$filter])) {
			throw new \RuntimeException('Could not find filter: '.$filter);
		}
		$filter = $this->filters[$filter];

		$reflection = new \ReflectionFunction($filter);
		$filterResult = call_user_func_array($filter, $this->prepareArguments($reflection));

		if($filterResult instanceof \Psr\Http\Message\ResponseInterface) {
			return $filterResult;
		} else if ($filterResult !== null) {
			die('Middleware returned other than null');
		} else {
			return null;
		}
	}

	private function callRoute($route, $args = []) : ResponseInterface {
		$response = null;

		if(isset($route['before'])) {
			$response = $this->callFilter($route['before']);
		}


		//finally call the route
		if($response === null) {
			$uses = $route['uses'];
			$reflection = null;

			if(is_callable($uses)) {
				$reflection = new \ReflectionFunction($uses);
				$response = call_user_func_array($uses, $this->prepareArguments($reflection, $args));
			} else if(is_string($uses)) {
				$act = explode('@', $uses);
				if(count($act) == 1) {
					$uses = $act;
					$reflection = new \ReflectionFunction($uses);
					$response = call_user_func_array($uses, $this->prepareArguments($reflection, $args));
				} else if(count($act) == 2) {
					$ctrl = $this->app->get($act[0]);
					$reflection = new \ReflectionMethod($ctrl, $act[1]);
					$response = call_user_func_array([$ctrl, $act[1]], $this->prepareArguments($reflection, $args));
				} else {
					throw new \RuntimeException('Could not parse route controller: '.$uses);
				}
			}
		}

		return $response;
	}

	private function prepareArguments($reflection, $args = []) {
		$fargs = [];
		foreach($reflection->getParameters() as $param) {
			$key = $param->getName();
			if(isset($args[$key])) {
				$fargs[$key] = $args[$key];
			}
			else if($param->getClass() !== null) {
				$name = $param->getClass()->getName();
				$bind = null;
				if($name == \Areus\Http\Request::class || $name == \Psr\Http\Message\ServerRequestInterface::class) {
					$bind = $this->request;
				} else {
					$bind = $this->app->get($param->getClass()->getName());
				}
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

	public function call404() : ResponseInterface {
		if(isset($this->routes['404']))
			return $this->callRoute($this->routes['404']);
		else
			throw new \RuntimeException('No action found for request: 404');
	}

	public function run(ServerRequestInterface $request) : ResponseInterface {
		$this->request = $request;
		$path = $request->getUri()->getPath();
		
		if($path == null) {
			if(isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])) {
				$path = $_SERVER['PATH_INFO'];
			} else {
				$path = explode('?', $_SERVER['REQUEST_URI'])[0];
			}
		}
		$path = '/'.trim($path, '/');

		$response = null;

		$missing = true;
		foreach($this->routes as $id => $route) {
			$pattern = $route['pattern'];
			if(isset($route['method']) && $route['method'] != strtolower($_SERVER['REQUEST_METHOD'])) {
				continue;
			}
			if($path == $pattern) {
				$response = $this->callRoute($route);
				$missing = false;
				break;
			} else if(preg_match($this->compile($route), $path, $matches) === 1) {
				$response = $this->callRoute($route, $matches);
				$missing = false;
				break;
			}
		}

		if($missing) {
			$response = $this->call404();
		}

		return $response;
	}

	public function dump() : string {
		$ret = '<pre>';
		foreach($this->routes as $id => $route) {
			$ret .= strtoupper($route['method']) . ' ' . $route['pattern']."\n";
			$ret .= $this->dumpArray($route, "\t");
		}
		$ret .= '</pre>';
		return $ret;
	}

	private function dumpArray($arr, $pre) : string {
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
