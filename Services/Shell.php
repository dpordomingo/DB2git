<?php

namespace Services;

use \Exception as Exception;

class Shell
{
	private $_unitOfWork = [];

	public function put ($stringCommand)
	{
		$this->_unitOfWork[] = $stringCommand;

		return $this;
	}

	public function exec ()
	{
		$commands = [];
		foreach ($this->_unitOfWork as $command) {
			$commands[] = sprintf('%s 2>&1', $command);
		}
		$stringCommands = implode('&&', $commands);

		exec($stringCommands, $output, $returnCode);

		if ($returnCode !== 0) {
			throw new Exception('Fail !!' . PHP_EOL . $stringCommands . PHP_EOL . var_export($output, true));
		}

		return [$returnCode, $output];
	}

	public function getQueue ()
	{
		return $this->_unitOfWork;
	}

	public function printQueue ()
	{
		var_dump($this->_unitOfWork);
		return $this;
	}

	public function killAll()
	{
		exit;
	}

}
