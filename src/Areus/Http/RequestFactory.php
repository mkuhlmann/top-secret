<?php declare(strict_types=1);

namespace Areus\Http;

use function Laminas\Diactoros\normalizeServer;
use function Laminas\Diactoros\normalizeUploadedFiles;
use function Laminas\Diactoros\marshalHeadersFromSapi;
use function Laminas\Diactoros\parseCookieHeader;
use function Laminas\Diactoros\marshalUriFromSapi;
use function Laminas\Diactoros\marshalMethodFromSapi;
use function Laminas\Diactoros\marshalProtocolVersionFromSapi;

abstract class RequestFactory {
	/**
	 * Function to use to get apache request headers; present only to simplify mocking.
	 *
	 * @var callable
	 */
	private static $apacheRequestHeaders = 'apache_request_headers';
	/**
	 * Create a request from the supplied superglobal values.
	 *
	 * If any argument is not supplied, the corresponding superglobal value will
	 * be used.
	 *
	 * The ServerRequest created is then passed to the fromServer() method in
	 * order to marshal the request URI and headers.
	 *
	 * @see fromServer()
	 * @param array $server $_SERVER superglobal
	 * @param array $query $_GET superglobal
	 * @param array $body $_POST superglobal
	 * @param array $cookies $_COOKIE superglobal
	 * @param array $files $_FILES superglobal
	 * @return Areus\Request
	 */
	public static function fromGlobals(
		array $server = null,
		array $query = null,
		array $body = null,
		array $cookies = null,
		array $files = null
	) : Request {
		$server = normalizeServer(
			$server ?: $_SERVER,
			is_callable(self::$apacheRequestHeaders) ? self::$apacheRequestHeaders : null
		);
		$files   = normalizeUploadedFiles($files ?: $_FILES);
		$headers = marshalHeadersFromSapi($server);
		if (null === $cookies && array_key_exists('cookie', $headers)) {
			$cookies = parseCookieHeader($headers['cookie']);
		}
		return new Request(
			$server,
			$files,
			marshalUriFromSapi($server, $headers),
			marshalMethodFromSapi($server),
			'php://input',
			$headers,
			$cookies ?: $_COOKIE,
			$query ?: $_GET,
			$body ?: $_POST,
			marshalProtocolVersionFromSapi($server)
		);
	}
}
