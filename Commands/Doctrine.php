<?php

namespace Panels\CommandPanel\Commands;

use \Panels\CommandPanel\Command;

class Doctrine {

	/* --- Public API --- */

	public static function register($panel, $group = 'Doctrine') {
		
		$panel->addCommand($group, 
				Command::create(
					'Create Schema',
					function ($container) {
						$em=$container->entityManager;
						$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
						$metadatas = $em->getMetadataFactory()->getAllMetadata();
						$schemaTool->createSchema($metadatas);
					}
				)
		);

		$panel->addCommand($group, 
				Command::create(
					'Update Schema',
					function ($container) {
						$em=$container->entityManager;
						$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
						$metadatas = $em->getMetadataFactory()->getAllMetadata();
						$schemaTool->updateSchema($metadatas);
					}
				)
		);

		$panel->addCommand($group, 
				Command::create(
					'Drop Schema',
					function ($container) {
						$em=$container->entityManager;
						$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
						$metadatas = $em->getMetadataFactory()->getAllMetadata();
						$schemaTool->dropSchema($metadatas);
					}
				)
		);
	}
}