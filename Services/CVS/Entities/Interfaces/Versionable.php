<?php

namespace Services\CVS\Entities\Interfaces;

interface Versionable
{
	public function isCompatible (Versionable $versionable);

	public function getCheckCode();

	public function setCheckCode($int);

	public function getName();

	public function getCode();

	public function generateNewCheckCode();
}