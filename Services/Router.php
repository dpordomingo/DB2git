<?php

namespace Services;

class Router 
{
	const CONTROLLER_SUFFIX = 'Controller';
	const CONTROLLER_NAMESPACE = 'Controllers';
	const CONTROLLER_PATH = __DIR__ . '/../' . self::CONTROLLER_NAMESPACE;
	const INDEX_NAME = 'list';

	private static function cleanedPath ($input)
	{
		$inputParts = false;
		preg_match('~^/?([a-zA-Z0-9_\-]+)~', $input, $inputParts);
		return ($inputParts && isset($inputParts[1]) ? $inputParts[1] : self::INDEX_NAME) . self::CONTROLLER_SUFFIX;
	}

	public static function serve ($container)
	{
		$requested = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : self::INDEX_NAME;
		$controllerName = ucfirst(self::cleanedPath($requested));
		$pathToServe = sprintf('%s/%s.php', self::CONTROLLER_PATH, $controllerName);

		if (!file_exists($pathToServe) || $pathToServe === self::cleanedPath($_SERVER['SCRIPT_NAME'])) {
			$controllerName = ucfirst(self::cleanedPath(self::INDEX_NAME));
			$pathToServe = sprintf('%s/%s.php', self::CONTROLLER_PATH, $controllerName);
		}
		include_once $pathToServe;

		$controllerFulQualifiedName = self::CONTROLLER_NAMESPACE . '\\' . $controllerName;
		$controller = new $controllerFulQualifiedName($container);
		return $controller->render();
	}
}