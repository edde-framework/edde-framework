<?php
	namespace Edde\Ext\Resource\Scanner;

	use Edde\Api\Resource\Scanner\IScanner;
	use phpunit\framework\TestCase;

	class FilesystemScannerTest extends TestCase {
		public function testCommon() {
			$scanner = $this->createScanner();
			$pathList = [];
			foreach ($scanner->scan() as $resource) {
				$pathList[] = str_replace(__DIR__ . '/assets\\', null, $resource->getUrl()
					->getPath());
			}
			$expected = [
				'bar\bar.txt',
				'bar\foobar.txt',
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
			return new FilesystemScanner(__DIR__ . '/assets');
		}
	}
