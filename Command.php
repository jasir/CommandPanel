<?php

namespace Panels\CommandPanel;

class Command extends \Nette\Object {

	/* --- Properties --- */

	/** @var text */
	private $title, $name, $description;

	/** @var callable */
	private $callback;

	/* --- Public API --- */

	public function __construct($title = NULL, $callback = NULL) {
		$this->title = $title;
		$this->callback = $callback;
	}

	public function getTitle() {
		return $this->title;
	}

	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}

	public function getName() {
		return \Nette\Utils\Strings::webalize($this->title);
	}

	public function getDescription() {
		return $this->description;
	}

	public function setDescription($description) {
		$this->description = $description;
		return $this;
	}

	public function setCallback($callback) {
		$this->callback = $callback;
		return $this;
	}

	public function invoke($container) {
		$result = call_user_func($this->callback, $container);
		return $result;
	}

	public static function create($title = NULL, $callback = NULL) {
		return new static($title, $callback);

}


	/* --- Hidden details --- */

}