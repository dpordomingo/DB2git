<?php

namespace Controllers;

use \Exception as Exception;

class EditController extends AbstractController
{
	public function render () 
	{
		$template = $this->container['TemplateRepo']->byId($_GET['id']);
		$oldCode = $template->code;
		$error = false;

		if (isset($_POST['code'])) {
			$template->code = $_POST['code'];
			$template->checkCode = $_POST['checkCode'];
			try {
				$this->container['EntityManager']->save($template);
			} catch (Exception $e) {
				$error = $e->getMessage();
			}
		}

		return $this->container['TPLEngine']->render('templateEdit', [
			'template' => $template,
			'oldCode' => $oldCode,
			'error' => $error
		]);
	}
}


