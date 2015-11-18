<?php

namespace DB;

use DB\Entities\AbstractDBBaseEntity;

class EntityManager
{
	public $dbConnector;

	private $_prePersistCallbacks = [];

	public function __construct (DBConnector $DBConnector)
	{
		$this->dbConnector = $DBConnector;
	}

	public function fetch ($mysqlQueryString)
	{
		return $this->dbConnector->fetch($mysqlQueryString);
	}

	public function save (AbstractDBBaseEntity $entity, $trigerListeners = true)
	{
		if ($trigerListeners) $this->_prePersist($entity);
		$this->_save($entity);
	}

	public function onPrePersist (callable $prePersistCallback)
	{
		$this->_prePersistCallbacks[] = $prePersistCallback;
	}

	private function _save (AbstractDBBaseEntity $entity)
	{
		$tableName = $entity->getDefinition('tableName');
		$setString = $this->_getSetString($entity);
		$queryString = sprintf('UPDATE %s SET %s WHERE id=%s', $tableName, $setString, $entity->getId());
		$this->dbConnector->execute($queryString);
	}

	private function _getSetString (AbstractDBBaseEntity $entity)
	{
		$storableFields = $entity->getDefinition('storableFields');
		$setArray = [];
		foreach ($storableFields as $storableField) {
			$setArray[] = sprintf('%s = "%s"', $storableField, $entity->{$storableField});
		}

		return implode(',', $setArray);
	}

	private function _prePersist (AbstractDBBaseEntity $entity)
	{
		foreach ($this->_prePersistCallbacks as $prePersistCallback) {
			$prePersistCallback($entity);
		}
	}
}