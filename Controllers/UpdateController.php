<?php

namespace Controllers;

class UpdateController extends AbstractController
{
	public function render () 
	{
		$templates = $this->container['TemplateRepo']->getAll();

		$updated = $this->container['CVS']->updateLocalRepository();

		return $this->container['TPLEngine']->render('templateUpdate', [
			'updated' => $updated
		]);
	}
}
