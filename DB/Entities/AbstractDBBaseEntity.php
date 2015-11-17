<?php 

namespace DB\Entities;

abstract class AbstractDBBaseEntity
{
	abstract function getDefinition($definitionKey);	

	abstract function getId();	
}