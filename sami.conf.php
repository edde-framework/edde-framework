<?php
	use Sami\Sami;
	use Sami\Version\GitVersionCollection;

	return new Sami($src = (__DIR__ . '/src'), [
		'title'                => 'Edde Framework',
		//'versions'             => GitVersionCollection::create($src)->addFromTags('*')->add('*'),
		'build_dir'            => __DIR__ . '/doc/%version%',
		'cache_dir'            => __DIR__ . '/temp/doc/cache/%version%',
		'default_opened_level' => 2,
	]);
