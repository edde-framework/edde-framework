<?php
	declare(strict_types = 1);

	namespace Edde\Common\Crate;

	use Edde\Api\Crate\ICrateGenerator;
	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Common\Cache\CacheFactory;
	use Edde\Common\Container\FactoryManager;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Schema\SchemaFactory;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Ext\Cache\InMemoryCacheStorage;
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
		protected $crateDirectory;

		public function testSimpleCrate() {
			foreach ($this->schemaManager->getSchemaList() as $schema) {
				$crateList = $this->crateGenerator->compile($schema);
				foreach ($crateList as $name => $source) {
					(function (IResource $resource) {
						require_once($resource->getUrl());
					})($this->crateDirectory->save(sha1($name) . '.php', $source));
					self::assertTrue(class_exists($name));
				}
			}
		}

		protected function setUp() {
			$this->schemaManager = new SchemaManager(new SchemaFactory(new ResourceManager()));
			$this->schemaManager->addSchema($header = new Header2Schema());
			$this->schemaManager->addSchema($item = new Item2Schema());
			$this->schemaManager->addSchema(new Row2Schema($header, $item));
			$this->crateDirectory = new CrateDirectory(__DIR__ . '/temp');
			$this->crateDirectory->purge();
			$this->crateGenerator = new CrateGenerator($this->schemaManager, $this->crateDirectory, new CacheFactory(__NAMESPACE__, new InMemoryCacheStorage()), new FactoryManager());
		}

		protected function tearDown() {
			$this->crateDirectory->delete();
		}
	}
