<?php

namespace Services\CVS;

use Services\CVS\Entities\Interfaces\Versionable;

use \Exception as Exception;
use Entities\TemplateRepository;
use Services\Shell;
use DB\EntityManager;

class CVS
{
	private $_repositoryPath;
	private $_origin;
	private $_branch;
	private $_templateRepo;
	private $_entityManager;

	public function __construct ($configArray, TemplateRepository $templateRepo, EntityManager $em)
	{
		$this->_repositoryPath = $configArray['repositoryPath'];
		$this->_origin = $configArray['originName'];
		$this->_branch = $configArray['branchName'];
		$this->_templateRepo = $templateRepo;
		$this->_entityManager = $em;
	}

	public function commit (Versionable $entity)
	{
		if (!$entity instanceof Versionable) return;

		if (!$this->_isLastVersion($entity)) {
			throw new Exception('Other user changed this entity before you.');
		}

		$this->_writeFile($entity);

		try{
			$this->_doCommit($entity);
			try{
				$this->_doPush();
			} catch (Exception $ex) {
				//throw $ex;
			}
		} catch (Exception $ex) {
			throw $ex;
		}

		$entity->generateNewCheckCode();
	}

	public function updateLocalRepository ()
	{
		if (!$this->canBeUpdatedFromOrigin()) {
			throw new Exception('Local repository can not be updated.');
		}

		try{
			$this->_doPull();
			try{
				return $this->_importFiles();
			} catch (Exception $ex) {
				//throw $ex;
			}
		} catch (Exception $ex) {
			throw $ex;
		}

		return [];
	}

	public function hasDivergedFromOrigin ()
	{
		$localRepoRev = $this->_getRevisionFromOrigin();
		return (Boolean)preg_match('~[1-9]+[0-9]*\s+[1-9]+[0-9]*~', $localRepoRev);
	}

	public function canBeUpdatedFromOrigin ()
	{
		$localRepoRev = $this->_getRevisionFromOrigin();
		return (Boolean)preg_match('~[1-9]+[0-9]*\s+0~', $localRepoRev);
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

	private function _importFiles()
	{
		$pattern = sprintf('%s/*.txt', $this->_repositoryPath);
		$filesNames = glob($pattern);

		$updatedTemplatesNames = [];
		foreach ($filesNames as $fileName) {
		    preg_match('~/([^/\.]+)\.txt~', $fileName, $templateNameParts);
		    $templateName = $templateNameParts[1];
		    $template = $this->_templateRepo->byName($templateName);
		    $templateImportedCode = file_get_contents($fileName);
		    if ($template->code !== $templateImportedCode) {
			    $template->code = $templateImportedCode;
			    $this->_entityManager->save($template, false);
			    $updatedTemplatesNames[] = $templateName;
		    } 
		}
		return $updatedTemplatesNames;
	}

	private function _doCommit(Versionable $entity)
	{
		$fileName = $this->_getFileName($entity);
		$commitMessage = sprintf('[CMS] %s', $entity->getName());

		$shell = new Shell();
		$shell
			->put(sprintf('cd %s', $this->_repositoryPath))
			->put(sprintf('git reset .'))
			->put(sprintf('git add %s', $fileName))
			->put(sprintf('git commit -m "%s"', $commitMessage))
			->exec();
	}

	private function _doPush()
	{
		$shell = new Shell();
		$shell->put(sprintf('cd %s', $this->_repositoryPath));

		if (!$this->hasDivergedFromOrigin()) {
			$shell->put(sprintf('git push %s %s', $this->_origin, $this->_branch));
		} else {
			$unsyncedBranchName = sprintf('unsynced_%d', time());
			$shell
				->put(sprintf('git checkout -B %s %s/%s', $unsyncedBranchName, $this->_origin, $this->_branch))
				->put(sprintf('git push %s %s --force', $this->_origin, $unsyncedBranchName))
				->put(sprintf('git checkout %s', $this->_branch))
				->put(sprintf('git push %s %s --force', $this->_origin, $this->_branch));
		}
		$shell->exec();
	}

	private function _doPull()
	{
		$shell = new Shell();
		$shell
			->put(sprintf('cd %s', $this->_repositoryPath))
			->put(sprintf('git pull --ff-only %s %s', $this->_origin, $this->_branch))
			->exec();
	}

	private function _getRevisionFromOrigin () 
	{
		$shell = new Shell();
		$res = $shell
			->put(sprintf('cd %s', $this->_repositoryPath))
			->put(sprintf('git fetch %s -v', $this->_origin))
			->put(sprintf('git rev-list --left-right --count %s/%s...%s', $this->_origin, $this->_branch, $this->_branch))
			->exec();

		return array_pop($res[1]);
	}

}
