<?php

namespace Controllers;

class ListController extends AbstractController
{
	public function render () 
	{
		$templates = $this->container['TemplateRepo']->getAll();

		return $this->container['TPLEngine']->render('templateList', ['templates' => $templates]);
	}
}



