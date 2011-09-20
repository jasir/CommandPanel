<?php

namespace Panels;

use
 Nette\Diagnostics\IBarPanel,
 Doctrine\ORM\EntityManager,
 Nette\Environment,
 Doctrine\ORM\Tools\SchemaTool;

/**
 * @author David Morávek
 * @author jasir
 */
class CommandPanel implements IBarPanel
{



	/**
	 * @param EntityManager $em
	 */
	public function __construct($em)
	{
		$this->processRequest($em);
	}



	/**
	 * IDebugPanel
	 *
	 * @return string
	 */
	public function getTab()
	{
		return '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAIAAACQkWg2AAABeUlEQVR4nGP8//8/AymAiSTVUA1f6kpfSXLC0cfEsF/HDv3///9LXelrdYlXkpzvXMy/9rT8////////DP/////379/nmZOfiHNA0Mfu5n8w8NLJ7E186D8kANXw79+/54Fu98U5IOjH5Yv//v37OHPyUyezPx8+YNfw58OHO6pit8TYb4mxP3Qy/bxt4x1VsV+PHvxDBQgN//79+7Rt4zUxdgi6qSr2/fKFfxgAJZR4PHx5wmN/MTD8YmD4/vHj708fMUOJEep3GPj15NEVY3UIm01WXnPfSWY+fqhSRsb///+jx8OT7hZOK7vfDAy/GRi+Pn74pLsFLgU1F9kPDzqbzzqY/vv370518SERdgh6f+Qgdk8/Xb7oqKHarw/vIdyTDqZ7Rdj3irAfVBaDCyI0PFm2aIcw28utG+ESHy9f2CHMBkHnYkPg4oz///8/7u/67ughBgYGDll5xYxcxbQcBgaGEwFuEEEI4NXRt91/EhpKJCc+kgAAZ1pXLNt3g34AAAAASUVORK5CYII=">Commands';
	}



	/**
	 * IDebugPanel
	 *
	 * @return string
	 */
	public function getPanel()
	{
		ob_start();
		require_once __DIR__ . '/command.panel.latte';
		return ob_get_clean();
	}



	/**
	 * IDebugPanel
	 *
	 * @return string
	 */
	public function getId()
	{
		return 'command-tool';
	}



	/**
	 * Ajax request process
	 * @var EntityManager
	 */
	public function processRequest($em)
	{
		$request = Environment::getHttpRequest();
		$response = Environment::getHttpResponse();

		if ($request->isPost() && $request->isAjax() && $request->getHeader('X-CommandTool-Client')) {

			$cmd = file_get_contents('php://input', TRUE);
			$schemaTool = new SchemaTool($em);
			$metadatas = $em->getMetadataFactory()->getAllMetadata();
			$message = array();
			try {
				switch ($cmd) {
					case 'create':
						$schemaTool->createSchema($metadatas);
						break;
					case 'update':
						$schemaTool->updateSchema($metadatas);
						break;
					case 'drop':
						$schemaTool->dropSchema($metadatas);
						break;

					default:
						throw new InvalidArgumentException('Invalid argument!');
						break;
				}
				$message['text'] = ucfirst($cmd) . ' query was successfully executed';
				$message['cls'] = 'success';
			} catch (\Exception $e) {
				$message['text'] = $e->getMessage();
				$message['cls'] = 'error';
			}
			$json = new \Nette\Application\Responses\JsonResponse($message);
			$json->send($request, $response);
			exit;
		}
	}



	/**
	 * @param EntityManager $em
	 */
	public static function register(EntityManager $em)
	{
		\Nette\Diagnostics\Debugger::addPanel(new static($em));
	}

}