<?php
	namespace Edde\Common\Crate;

	use Edde\Api\Crate\ICrateGenerator;
	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Common\File\TempDirectory;
	use Edde\Common\Schema\SchemaFactory;
	use Edde\Common\Schema\SchemaManager;
	use Foo\Bar\Header2Schema;
	use Foo\Bar\Item2Schema;
	use Foo\Bar\Row2Schema;
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
					}, $this->tempDirectory->save(sha1($name) . '.php', $source));
					self::assertTrue(class_exists($name));
				}
			}
		}

		protected function setUp() {
			$this->schemaManager = new SchemaManager(new SchemaFactory());
			$this->schemaManager->addSchema($header = new Header2Schema());
			$this->schemaManager->addSchema($item = new Item2Schema());
			$this->schemaManager->addSchema(new Row2Schema($header, $item));
			$this->crateGenerator = new CrateGenerator();
			$this->tempDirectory = new TempDirectory(__DIR__ . '/temp');
			$this->tempDirectory->purge();
		}

		protected function tearDown() {
			$this->tempDirectory->delete();
		}
	}
