<?php

namespace Entities;

use DB\EntityManager;

class TemplateRepository
{
	public $entityManager;

	public function __construct (EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	public function getAll ()
	{
		$queryString = 'SELECT * FROM Templates';
		$templatesArr = $this->entityManager->fetch($queryString);
		$templates = [];
		foreach ($templatesArr as $templateArr) {
			$templates[] = new Template($templateArr);
		}

		return $templates;
	}

	public function byId ($id)
	{
		$queryString = sprintf('SELECT * FROM Templates WHERE id = %d', $id);
		$templatesArr = $this->entityManager->fetch($queryString);

		if (count($templatesArr)) {
			return new Template($templatesArr[0]);
		} else {
			return null;
		}
	}
}