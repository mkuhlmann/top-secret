<?php

declare(strict_types=1);

function app(): Areus\Application
{
	return \Areus\Application::getInstance();
}

function view($file, $data = [])
{
	return app()->view->make($file, $data);
}

function viewResponse($file, $data = [], $code = 200)
{
	return new \Areus\Http\Response\ViewResponse(view($file, $data), $code);
}

if (!function_exists('e')) {
	function e($value)
	{
		return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
	}
}

/**
 * Get all of the given array except for a specified array of keys.
 *
 * @param  array  $array
 * @param  array|string  $keys
 *
 * @return array
 */
function array_except(array $array, array $keys): array
{
	foreach ($keys as $key) {
		unset($array[$key]);
	}
	return $array;
}

function dbdate($timestamp = null): string
{
	if ($timestamp != null) {
		return gmdate('Y-m-d H:i:s', $timestamp);
	}
	return gmdate('Y-m-d H:i:s');
}

function nanoid($size = 21): string
{
	$alphabet = '-0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz';
	$nanoid = '';
	for ($i = 0; $i < $size; $i++) {
		$nanoid .= substr($alphabet, mt_rand(0, strlen($alphabet)), 1);
	}
	return $nanoid;
}

function tzdate(string $format, string|int $timestamp = null, string $timezone = 'UTC'): string
{
	if (!is_numeric($timestamp)) {
		$timestamp = strtotime($timestamp);
	}
	date_default_timezone_set($timezone);
	$time = date($format, $timestamp ?: time());
	date_default_timezone_set('UTC');
	return $time;
}
