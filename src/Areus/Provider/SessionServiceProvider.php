<?php

namespace Areus\Provider;

class SessionServiceProvider extends ApplicationServiceProvider
{
	public function provides(string $id): bool
	{
		return $id == 'session' || $id == \Areus\Session\SessionManager::class;
	}

	public function register(): void
	{
		/** @var \League\Container\Container */
		$container = $this->getContainer();
		$app = $this->getApp();

		$sessionHandler = null;

		switch ($app->config->get('areus.session.driver')) {
			case 'file':
				$sessionHandler = new \Areus\Session\SessionHandlerFilesystem(
					$app->path('storage/sessions'),
					$app->config->get('areus.session.lifetime')
				);
				break;
			default:
				$sessionHandler = $app->get($app->config->get('areus.session.driver'));
				break;
		}

		$container
			->add(\Areus\Session\SessionManager::class)
			->addArguments([
				$sessionHandler,
				$app->config->get('areus.session')
			])
			->addTag('session')
			->setShared(true);
	}
}
