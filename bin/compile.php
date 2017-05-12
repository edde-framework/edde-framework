#!/usr/bin/env php
<?php
	declare(strict_types=1);

	use Edde\Common\File\FileUtils;
	use Edde\Framework;

	require_once __DIR__ . '/../loader.php';

	if (class_exists('Phar') === false || ini_get('phar.readonly')) {
		echo "Enable Phar extension and set directive 'phar.readonly=off'.\n";
		exit(1);
	}

	FileUtils::delete(__DIR__ . '/../release/temp');

	FileUtils::copy(__DIR__ . '/../src', __DIR__ . '/../release/temp/edde-framework/src');
	FileUtils::copy(__DIR__ . '/../src', __DIR__ . '/../release/temp/edde-framework.bundle/src');
	FileUtils::copy(__DIR__ . '/../lib', __DIR__ . '/../release/temp/edde-framework.bundle/lib');
	FileUtils::copy(__DIR__ . '/../loader.php', __DIR__ . '/../release/temp/edde-framework.bundle/loader.php');

	function build(string $file, string $source, string $stub) {
		@unlink($file);
		$phar = new \Phar($file, 0, $pharFile = basename($file));
		$phar->setStub(str_replace('{phar-file}', $pharFile, $stub));
		$phar->buildFromDirectory($source);
		$phar->compressFiles(\Phar::GZ);
	}

	$name = json_decode(file_get_contents(__DIR__ . '/../composer.json'));
	build($phar = (__DIR__ . '/../release/' . ($base = 'edde-framework-' . (new Framework())->getVersion()) . '.phar'), __DIR__ . '/../release/temp/edde-framework', '<?php Phar::mapPhar("{phar-file}"); require_once("phar://{phar-file}/src/loader.php"); __HALT_COMPILER();');
	build($bundle = (__DIR__ . '/../release/' . $base . '-bundle.phar'), __DIR__ . '/../release/temp/edde-framework.bundle', '<?php Phar::mapPhar("{phar-file}"); require_once("phar://{phar-file}/loader.php"); __HALT_COMPILER();');

	FileUtils::copy($phar, __DIR__ . '/../release/edde-framework.phar');
	FileUtils::copy($bundle, __DIR__ . '/../release/edde-framework-bundle.phar');
