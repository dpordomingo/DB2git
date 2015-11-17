<?php

namespace Entities;

use Services\CVS\Entities\Interfaces\Versionable;
use DB\Entities\AbstractDBBaseEntity;

class Template extends AbstractDBBaseEntity implements Versionable
{
	public $id;
	public $name;
	public $code;
	public $checkCode;

	private static $_DEFINITIONS = [
		'tableName' => 'Templates',
		'storableFields' => ['code', 'checkCode']
	];

	public function __construct ($arrayData) 
	{
		$this->id = $arrayData['id'];
		$this->name = $arrayData['name'];
		$this->code = $arrayData['code'];
		$this->checkCode = $arrayData['checkCode'];
	}

	public function isCompatible (Versionable $versionable)
	{
		return $versionable->getCheckCode() === $this->getCheckCode();
	}

	public function getId()
	{
		return $this->id;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getCode()
	{
		return $this->code;
	}

	public function getCheckCode()
	{
		return $this->checkCode;
	}

	public function setCheckCode($int)
	{
		return $this->checkCode = $int;
	}

	public function getDefinition ($definitionKey = null)
	{
		return $definitionKey ? self::$_DEFINITIONS[$definitionKey] : self::$_DEFINITIONS;
	}

	public function generateNewCheckCode()
	{
		$this->checkCode = rand(100000, PHP_INT_MAX);
	}
}


