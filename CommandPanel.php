<?php

namespace Panels\CommandPanel;

use
 Nette\Diagnostics\IBarPanel,
 Doctrine\ORM\EntityManager,
 Nette\Environment,
 Nette\DI\Container,
 Doctrine\ORM\Tools\SchemaTool;


/**
 * @author David Morávek
 * @author jasir
 */
class Panel extends \Nette\Object implements IBarPanel
{


	/**
	 * @var \Nette\DI\Container
	 */
	private $container;

	private $commands = array();

	public function __construct(\Nette\DI\Container $container) {
		$this->container = $container;
	}

	public function addCommand($group, $command)
	{
		$this->commands[$group][$command->name] = $command;
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
		$template = new \Nette\Templating\FileTemplate;
		$template->setCacheStorage(Environment::getContext()->templateCacheStorage);
		$template->setFile(dirname(__FILE__) . "/command.panel.latte");
		$template->registerFilter(new \Nette\Latte\Engine());
		$template->presenter = Environment::getApplication()->getPresenter();
		$template->commands = $this->commands;
		ob_start();
		$template->render();
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
	protected function processRequest()
	{
		$request = $this->container->httpRequest;
		$response = $this->container->httpResponse;

		if ($request->isPost() && $request->isAjax() && $request->getHeader('X-CommandTool-Client')) {

			$cmd = file_get_contents('php://input', TRUE);
				try {
					list($group, $name) = explode(':::', $cmd);
					$command = \Nette\Utils\Arrays::get($this->commands, array($group, $name), NULL);
					$result = $command->invoke($this->container);
					$message['text'] = $result;
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
	public function register(\Nette\Diagnostics\Bar $bar) {
		$this->processRequest();
		\Nette\Diagnostics\Debugger::$bar->addPanel($this);
	}

}