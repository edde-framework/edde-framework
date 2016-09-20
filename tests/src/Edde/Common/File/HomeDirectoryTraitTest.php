<?php
	declare(strict_types = 1);

	namespace Edde\Common\File;

	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets.php');

	class HomeDirectoryTraitTest extends TestCase {
		public function testCommon() {
			$home = new \HomeTest($dir = 'moo/bar/poo');
			$home->lazyRootDirectory($root = new RootDirectory(__DIR__));
			$dir = $root->directory($dir);
			$dir->delete();
			self::assertFalse($dir->exists());
			$home->use();
			self::assertFalse($dir->exists());
			$home->getHome();
			self::assertTrue($dir->exists());
		}
	}
