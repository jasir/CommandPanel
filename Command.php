<?php

namespace Panels\CommandPanel;

class Command extends \Nette\Object {

	/* --- Properties --- */

	/** @var text */
	private $title, $name, $description;

	/** @var callable */
	private $callback;

	/* --- Public API --- */

	public function __construct($title, $name, $description, $callback) {
		$this->title = $title;
		$this->name  = $name;
		$this->callback = $callback;
		$this->description = $description;
	}

	public function getTitle() {
		return $this->title;
	}

	public function getName() {
		return $this->name;
	}

	public function getDescription() {
		return $this->description;
	}

	public function invoke($container) {
		$result = call_user_func($this->callback, $container);
		return $result;
	}


	/* --- Hidden details --- */

}