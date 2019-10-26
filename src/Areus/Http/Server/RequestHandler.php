<?php

declare(strict_types=1);

namespace Areus\Http\Server;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Traversable;
use TypeError;
use function count;

/**
 * PSR-15 Request Handler
 */
class RequestHandler implements RequestHandlerInterface {
	/**
	 * @var array|Traversable
	 */
	protected $queue;

	/**
	 * @var callable
	 */
	protected $resolver;

	/**
	 *
	 * @param array|Traversable $queue A queue of middleware entries.
	 * @param callable $resolver Converts queue entries to middleware
	 * instances.
	 */
	public function __construct($queue, callable $resolver = null)
	{
		if (!is_iterable($queue)) {
			throw new TypeError('\$queue must be array or Traversable.');
		}

		if (count($queue) === 0) {
			throw new InvalidArgumentException('$queue cannot be empty');
		}
		
		$this->queue = $queue;
		
		if ($resolver === null) {
			$resolver = function ($entry) {
				return $entry;
			};
		}

		$this->resolver = $resolver;

		reset($this->queue);
	}

	public function handle(ServerRequestInterface $request) : ResponseInterface {
		$entry = current($this->queue);

        $middleware = call_user_func($this->resolver, $entry);
		next($this->queue);
		
        if ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
		}
		
        return $middleware($request, $this);
	}
}