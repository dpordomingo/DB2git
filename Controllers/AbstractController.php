<?php

namespace Controllers;

abstract class AbstractController
{
	public $container;

	public function __construct ($container)
	{
		$this->container = $container;
	}
	
	public abstract function render();
}