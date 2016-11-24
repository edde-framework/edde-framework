<?php
	/**
	 * file responsible for requiring all dependencies
	 */
	declare(strict_types = 1);

	use Edde\Api\File\IRootDirectory;
	use Edde\Common\File\RootDirectory;

	require_once __DIR__ . '/vendor/autoload.php';
	require_once __DIR__ . '/../src/loader.php';

	return array_merge([
		IRootDirectory::class => new RootDirectory(__DIR__),
	], is_array($local = @include __DIR__ . '/loader.local.php') ? $local : []);
