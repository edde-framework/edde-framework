<?php
	namespace Edde\Common\Crate;

	use Edde\Api\Crate\ICrateGenerator;
	use Edde\Api\File\ITempDirectory;
	use Edde\Common\File\TempDirectory;
	use Edde\Common\Schema\Schema;
	use Edde\Common\Schema\SchemaProperty;
	use phpunit\framework\TestCase;

	class CrateGeneratorTest extends TestCase {
		/**
		 * @var ICrateGenerator
		 */
		protected $crateGenerator;
		/**
		 * @var ITempDirectory
		 */
		protected $tempDirectory;

		public function testSimpleCrate() {
			$schema = new Schema('Foo\\Bar\\FooBar');
			$schema->addPropertyList([
				new SchemaProperty($schema, 'guid', null, true, true, true),
				new SchemaProperty($schema, 'name'),
				new SchemaProperty($schema, 'some-long-named-property'),
			]);
			$crateList = $this->crateGenerator->generate($schema);
			foreach ($crateList as $name => $source) {
				$this->tempDirectory->file(sha1($name) . '.php', $source);
			}
		}

		protected function setUp() {
			$this->crateGenerator = new CrateGenerator();
			$this->tempDirectory = new TempDirectory(__DIR__ . '/temp');
			$this->tempDirectory->purge();
		}

		protected function tearDown() {
			$this->tempDirectory->delete();
		}
	}
