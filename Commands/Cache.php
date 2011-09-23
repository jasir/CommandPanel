<?php

namespace Panels\CommandPanel\Commands;

use \Panels\CommandPanel\Command;

class Cache {

	/* --- Properties --- */

	/* --- Public API --- */

	public static function register($panel, $group = 'Cache & Session') {
		$panel->addCommand($group, 
				Command::create(
					'Clear all',
					function ($container) {
						$container->cacheStorage->clean(array(\Nette\Caching\Cache::ALL => TRUE));
						return 'Cache cleared';
					}
		));
		
		$panel->addCommand($group, 
				Command::create(
					'Rebuild RobotLoader Cache',
					function ($container) {
						$container->robotLoader->rebuild();
						return 'Robotloader cache rebuild';
					}
		));		

		$panel->addCommand($group, 
				Command::create(
					'Destroy Session',
					function ($container) {
						$container->session->destroy();
						return 'Destroyed. Enjoy.';
					})
					->setDescription('Destroy all session data.')
					);		
	
	
	}
/* --- Hidden details --- */

}