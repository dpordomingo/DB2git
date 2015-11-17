<?php

use DB\DBConnector;
use DB\EntityManager;
use League\Plates\Engine as TPLEngine;
use Entities\TemplateRepository;
use Services\Router;
use Services\CVS\CVS;

require_once 'vendor/autoload.php';

$APP = [];
$APP['DB'] = new DBConnector('10.0.0.5', 'root', 'root', 'git');
$APP['TPLEngine'] = new TPLEngine(__DIR__ . '/resources/views');
$APP['EntityManager'] = new EntityManager($APP['DB']);
$APP['TemplateRepo'] = new TemplateRepository($APP['EntityManager']);
$APP['CVS'] = new CVS(__DIR__ . '/resources/DB2git-templates', $APP['TemplateRepo']);

$APP['EntityManager']->onPrePersist(
	function ($entity) use ($APP) {
		$APP['CVS']->commit($entity);
	}
);

echo Router::serve($APP);
