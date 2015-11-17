<?php

namespace DB;

use \mysqli as mysqli;

class DBConnector
{
	public $connection;

	public function __construct ($dbHostName, $dbUserName, $dbPass, $dbName)
	{
		$this->connection = new mysqli($dbHostName, $dbUserName, $dbPass, $dbName);
	}

	public function fetch($mySQLQueryString)
	{
		$res = $this->execute($mySQLQueryString);
		$result = [];

		return $res->fetch_all(MYSQLI_ASSOC);
	}

	public function execute($mySQLQueryString)
	{
		$res = $this->connection->query($mySQLQueryString);

		return $res;
	}

}