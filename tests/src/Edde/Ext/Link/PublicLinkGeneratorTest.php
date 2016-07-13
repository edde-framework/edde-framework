<?php
	namespace Edde\Ext\Link;

	use Edde\Api\Resource\ResourceException;
	use Edde\Common\File\FileUtils;
	use Edde\Common\Resource\Resource;
	use phpunit\framework\TestCase;

	class PublicLinkGeneratorTest extends TestCase {
		public function testCommon() {
			$publicLinkGenerator = new PublicLinkGenerator(__DIR__, __DIR__ . '/public');
			self::assertFalse($publicLinkGenerator->isUsed());
			file_put_contents(__DIR__ . '/assets/file', $data = mt_rand(0, 9999999));
			$url = $publicLinkGenerator->generate(new Resource(FileUtils::url(__DIR__ . '/assets/file')));
			self::assertFileExists($file = (__DIR__ . $url->getPath()));
			self::assertEquals($data, file_get_contents($file));
		}

		public function testTypeKaboom() {
			$this->expectException(ResourceException::class);
			$this->expectExceptionMessage('Unsuported type for [string]; parameter should be instance of [Edde\Api\Resource\IResource].');
			$publicLinkGenerator = new PublicLinkGenerator(__DIR__, __DIR__ . '/public');
			$publicLinkGenerator->generate('poo');
		}

		public function testPathKaboom() {
			$this->expectException(ResourceException::class);
			$this->expectExceptionMessage(sprintf('Root folder [%s] should be subpath of the public folder [%s/public].', PHP_BINDIR, __DIR__));
			$publicLinkGenerator = new PublicLinkGenerator(PHP_BINDIR, __DIR__ . '/public');
			$publicLinkGenerator->generate(null);
		}
	}
