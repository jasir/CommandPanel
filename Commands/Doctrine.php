<?php

namespace Panels\CommandPanel\Commands;

class Doctrine {

	/* --- Properties --- */

	/* --- Public API --- */

	public static function register($panel, $group = 'Doctrine') {
		$panel->addCommand($group, new \Panels\CommandPanel\Command(
			'Create schema',
			'orm:create-schema',
			'Creates doctrine schema',
			function ($container) {
				$em=$container->entityManager;
				$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
				$metadatas = $em->getMetadataFactory()->getAllMetadata();
				$schemaTool->createSchema($metadatas);
				return 'Schema created';
			}
		));
		$panel->addCommand($group, new \Panels\CommandPanel\Command(
			'Update scheme',
			'orm:update-scheme',
			'Updates doctrine schema',
			function ($container) {
				$em=$container->entityManager;
				$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
				$metadatas = $em->getMetadataFactory()->getAllMetadata();
				$schemaTool->updateSchema($metadatas);
				return 'Schema updated';
			}
		));
		$panel->addCommand($group, new \Panels\CommandPanel\Command(
			'Drop scheme',
			'orm:drop-scheme',
			'Drops doctrine scheme',
			function ($container) {
				$em=$container->entityManager;
				$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
				$metadatas = $em->getMetadataFactory()->getAllMetadata();
				$schemaTool->dropSchema($metadatas);
				return 'Schema dropped';
			}
		));
	}

	/* --- Hidden details --- */

}