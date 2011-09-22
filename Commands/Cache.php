<?php

namespace Panels\CommandPanel\Commands;

class Cache {

	/* --- Properties --- */

	/* --- Public API --- */

	public static function register($panel, $group = 'Cache & Session') {
		$panel->addCommand($group, new \Panels\CommandPanel\Command(
			'Clear all',
			'cache:clear',
			NULL,
			function ($container) {
				$container->cacheStorage->clean(array(\Nette\Caching\Cache::ALL => TRUE));
				return 'Cache cleared';
			}
		));
		$panel->addCommand($group, new \Panels\CommandPanel\Command(
			'Rebuild RobotLoader',
			'robotloader:clear',
			NULL,
			function ($container) {
				$container->robotLoader->rebuild();
				return 'Robotloader cache rebuild';
			}
		));
		$panel->addCommand($group, new \Panels\CommandPanel\Command(
			'Destroy session',
			'session:destroy',
			NULL,
			function ($container) {
				$container->session->destroy();
				return 'Destroyed. Enjoy.';
			}
		));

	}

	/* --- Hidden details --- */

}