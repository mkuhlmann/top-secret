<?php

namespace Areus;

class SessionHandlerFilesystem implements \SessionHandlerInterface {
	protected $path;
	protected $lifetime;

	public function __construct($path, $lifetime = 120) {
		if(!file_exists($path)) {
			mkdir($path, 0777, true);
		}
		$this->path = $path;
		$this->lifetime = $lifetime;
	}

	public function open($savePath, $sessionId)
	{
		return true;
	}

	public function close()
	{
		return true;
	}

	public function read($sessionId) {
		if(file_exists($this->path.'/'.$sessionId) && filemtime($this->path.'/'.$sessionId) >= time() - $this->lifetime * 60) {
			return file_get_contents($this->path.'/'.$sessionId);
		}
		return null;
	}

	public function write($sessionId, $data) {
		file_put_contents($this->path.'/'.$sessionId, $data);
	}

	public function destroy($sessionId) {
		if(file_exists($this->path.'/'.$sessionId)) {
			unlink($this->path.'/'.$sessionId);
		}
	}

	public function gc($lifetime) {

	}
}
