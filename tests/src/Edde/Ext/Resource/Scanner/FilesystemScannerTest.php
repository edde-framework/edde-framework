<?php
	namespace Edde\Ext\Resource\Scanner;

	use Edde\Api\Resource\Scanner\IScanner;
	use Edde\Common\File\Directory;
	use phpunit\framework\TestCase;

	class FilesystemScannerTest extends TestCase {
		public function testCommon() {
			$scanner = $this->createScanner();
			$pathList = [];
			foreach ($scanner->scan() as $resource) {
				$path = str_replace('\\', '/', $resource->getUrl()
					->getPath());
				$pathList[] = str_replace(str_replace('\\', '/', __DIR__ . '/assets/'), null, $path);
			}
			$expected = [
				'bar/bar.txt',
				'bar/foobar.txt',
				'foo.txt',
			];
			sort($expected);
			sort($pathList);

			self::assertEquals($expected, $pathList);
		}

		/**
		 * @return IScanner
		 */
		protected function createScanner() {
			return new FilesystemScanner(new Directory(__DIR__ . '/assets'));
		}
	}
