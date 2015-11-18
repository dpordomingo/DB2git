<?php

namespace Controllers;

class ListController extends AbstractController
{
	public function render () 
	{
		$templates = $this->container['TemplateRepo']->getAll();

		$CVS = $this->container['CVS'];
		$isUpdatable = $CVS->canBeUpdatedFromOrigin();
		$isUnsynced = $CVS->hasDivergedFromOrigin();

		return $this->container['TPLEngine']->render('templateList', [
			'templates' => $templates,
			'isUpdatable' => $isUpdatable,
			'isUnsynced' => $isUnsynced
		]);
	}
}



