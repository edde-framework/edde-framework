<?php
	namespace Edde\Common\Crate;

	use Edde\Api\Crate\ICrateGenerator;
	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Common\File\TempDirectory;
	use Edde\Common\Schema\SchemaManager;
	use Foo\Bar\HeaderSchema;
	use Foo\Bar\ItemSchema;
	use Foo\Bar\RowSchema;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/schema.php');

	class CrateGeneratorTest extends TestCase {
		/**
		 * @var ISchemaManager
		 */
		protected $schemaManager;
		/**
		 * @var ICrateGenerator
		 */
		protected $crateGenerator;
		/**
		 * @var ITempDirectory
		 */
		protected $tempDirectory;

		public function testSimpleCrate() {
			foreach ($this->schemaManager->getSchemaList() as $schema) {
				$crateList = $this->crateGenerator->generate($schema);
				foreach ($crateList as $name => $source) {
					call_user_func(function (IResource $resource) {
						require_once($resource->getUrl());
					}, $this->tempDirectory->file(sha1($name) . '.php', $source));
					self::assertTrue(class_exists($name));
				}
			}
		}

		protected function setUp() {
			$this->schemaManager = new SchemaManager();
			$this->schemaManager->addSchema($header = new HeaderSchema());
			$this->schemaManager->addSchema($item = new ItemSchema());
			$this->schemaManager->addSchema(new RowSchema($header, $item));
			$this->crateGenerator = new CrateGenerator();
			$this->tempDirectory = new TempDirectory(__DIR__ . '/temp');
			$this->tempDirectory->purge();
		}

		protected function tearDown() {
			$this->tempDirectory->delete();
		}
	}
