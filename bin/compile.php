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

	$name = 'edde-framework';
	$version = $name . '-' . (new Framework())->getVersion();

	$rootDir = realpath(__DIR__ . '/..');
	$releaseDir = $rootDir . '/release';
	$pharDir = $releaseDir . '/phar';
	$bundleDir = $releaseDir . '/bundle';
	$sourceDir = $rootDir . '/src';

	FileUtils::copy($sourceDir, $pharDir . '/src');
	FileUtils::copy($sourceDir, $bundleDir . '/src');
	FileUtils::copy($rootDir . '/loader.php', $bundleDir . '/loader.php');

	function make(string $file, string $source, string $stub) {
		$phar = new \Phar($file, 0, $pharFile = basename($file));
		$phar->setStub(str_replace('{phar-file}', $pharFile, $stub));
		$phar->buildFromDirectory($source);
		$phar->compressFiles(\Phar::GZ);
	}

	make($releaseDir . '/' . $version . '.phar', $pharDir, '<?php Phar::mapPhar("{phar-file}"); require_once("phar://{phar-file}/src/loader.php"); __HALT_COMPILER();');
	make($releaseDir . '/' . $name . '.phar', $pharDir, '<?php Phar::mapPhar("{phar-file}"); require_once("phar://{phar-file}/src/loader.php"); __HALT_COMPILER();');
	make($releaseDir . '/' . $version . '.bundle.phar', $sourceDir, '<?php Phar::mapPhar("{phar-file}"); require_once("phar://{phar-file}/loader.php"); __HALT_COMPILER();');
	make($releaseDir . '/' . $name . '.bundle.phar', $sourceDir, '<?php Phar::mapPhar("{phar-file}"); require_once("phar://{phar-file}/loader.php"); __HALT_COMPILER();');

	exit(0);
