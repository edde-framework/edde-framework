<?php
	declare(strict_types = 1);

	namespace Edde;

	use Edde\Common\Autoloader;

	require_once __DIR__ . '/Common/Autoloader.php';

	/**
	 * I hate a magic constants, but this one is a needed hell encapsulated into the system; no one should use this or The God will kill... no there will be no
	 * cute kittens at that time; The God will make a suicide.
	 */
	define('EDDE_ROOT_DIRECTORY', __DIR__);
	Autoloader::register(__NAMESPACE__, EDDE_ROOT_DIRECTORY);
