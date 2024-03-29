<?php
/**
 * @todo - odstranit z commandu name - zbytecne, mel by se generovat sam jako webalize
 * @todo - command - pridat create pro fluent interface + konstruktor bez parametru
 */


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

	public function addCommand($group, $command) {
		if (isset($this->commands[$group][$command->name])) {
			throw new \Nette\InvalidStateException("Panels\CommandPanel : Command '{$command->name}' is already in group '{$group}'");
		}
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
					list($group, $name) = explode(':::', $cmd);
					$command = \Nette\Utils\Arrays::get($this->commands, array($group, $name), NULL);
					\Nette\Diagnostics\Debugger::timer('CommandPanel');
					ob_start();
					try {
						$result = $command->invoke($this->container);
						$output = ob_get_contents();
						ob_end_clean();
						$time = \Nette\Diagnostics\Debugger::timer('CommandPanel');
						if ($result === NULL) {
							$result = $command->title . ' - OK';
						}
						$message['text'] = $result . "<br><small>" . number_format($time,4) . " seconds</small>";
						$message['cls'] = 'success';
						$message['output'] = $output;
					} catch (\Exception $e) {
						$output = ob_get_contents();
						ob_end_clean();
						$message['text'] = $e->getMessage();
						$message['cls'] = 'error';
						$message['output'] = $output;
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