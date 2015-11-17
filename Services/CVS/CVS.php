<?php

namespace Services\CVS;

use Services\CVS\Entities\Interfaces\Versionable;

use \Exception as Exception;
use Entities\TemplateRepository;

class CVS
{
	private $_repositoryPath;
	private $_templateRepo;

	public function __construct ($repositoryPath, TemplateRepository $templateRepo)
	{
		$this->_repositoryPath = $repositoryPath;
		$this->_templateRepo = $templateRepo;
	}

	public function commit (Versionable $entity)
	{
		if (!$entity instanceof Versionable) return;

		if (!$this->_isLastVersion($entity)) {
			throw new Exception('Other user changed this entity before you.');
		}

		$this->_writeFile($entity);
		$this->_doCommit($entity);

		//$this->_doPush($entity);
		$entity->generateNewCheckCode();
	}

	private function _isLastVersion(Versionable $entity)
	{
		$savedEntity = $this->_templateRepo->byId($entity->getId());
		return $savedEntity->isCompatible($entity);
	}

	private function _getFileName(Versionable $entity)
	{
		return sprintf('%s/%s.txt', $this->_repositoryPath , $entity->getName());
	}

	private function _writeFile(Versionable $entity)
	{
		file_put_contents($this->_getFileName($entity), $entity->getCode());
	}

	private function _doCommit(Versionable $entity)
	{
		$fileName = $this->_getFileName($entity);
		$commitMessage = sprintf('[CMS] %s', $entity->getName());
		$commandString = 'cd ' . $this->_repositoryPath . ' &&
			git reset . &&
			git add ' . $fileName . ' &&
			git commit -m "' . $commitMessage . '"';
		exec($commandString, $output, $returnCode);
		if ($returnCode !== 0) {
			throw new Exception('Commit failed.' . PHP_EOL . $commandString . PHP_EOL . var_export($output, true));
		}
	}

}