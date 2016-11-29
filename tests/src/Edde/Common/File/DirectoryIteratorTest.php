<?php
	declare(strict_types = 1);

	namespace Edde\Common\File;

	use Edde\Api\File\IDirectoryIterator;
	use PHPUnit\Framework\TestCase;

	class DirectoryIteratorTest extends TestCase {
		/**
		 * @var IDirectoryIterator
		 */
		protected $directoryIterator;

		public function testDirectoryIterator() {
			$this->directoryIterator->registerOnUse(function (IDirectoryIterator $directoryIterator) {
				$directoryIterator->addDirectory(new Directory(__DIR__ . '/foo'));
				$directoryIterator->addDirectory(new Directory(__DIR__ . '/bar'));
			});
			$files = [];
			foreach ($this->directoryIterator as $file) {
				$files[] = $file->getName();
			}
			sort($files);
			self::assertEquals([
				'a',
				'b',
				'c',
			], $files);
		}

		protected function setUp() {
			$this->directoryIterator = new DirectoryIterator();
		}
	}
