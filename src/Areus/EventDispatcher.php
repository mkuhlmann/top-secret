<?php

namespace Areus;

trait EventDispatcher {
	protected $eventListener = [];

	public function on($event, $listener) {
		if(!isset($this->eventListener[$event])) {
			$this->eventListener[$event] = [];
		}
		$this->eventListener[$event][] = $listener;
	}

	protected function emit($event, $payload = null) {
		if(!isset($this->eventListener[$event])) {
			return;
		}
		foreach($this->eventListener[$event] as $listener) {
			$listener($payload);
		}
	}
}
