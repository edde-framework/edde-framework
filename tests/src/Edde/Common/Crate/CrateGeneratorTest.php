<?php
	declare(strict_types = 1);

	namespace Edde\Common\Crate;

	use Edde\Api\Cache\ICacheFactory;
	use Edde\Api\Crate\ICrateDirectory;
	use Edde\Api\Crate\ICrateGenerator;
	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Schema\ISchemaFactory;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Common\Cache\DummyCacheFactory;
	use Edde\Common\Schema\SchemaFactory;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Ext\Container\ContainerFactory;
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
					$reflectionClass = new \ReflectionClass($name);
					foreach ($schema->getMeta('implements', []) as $meta) {
						self::assertContains($meta, $reflectionClass->getInterfaceNames());
					}
				}
			}
		}

		protected function setUp() {
			$container = ContainerFactory::create([
				ISchemaManager::class => SchemaManager::class,
				ISchemaFactory::class => SchemaFactory::class,
				ICrateGenerator::class => CrateGenerator::class,
				ICrateDirectory::class => function () {
					return $this->crateDirectory = new CrateDirectory(__DIR__ . '/temp');
				},
				ICacheFactory::class => DummyCacheFactory::class,
			]);

			$this->schemaManager = $container->create(ISchemaManager::class);
			$this->schemaManager->addSchema($header = new Header2Schema());
			$this->schemaManager->addSchema($item = new Item2Schema());
			$this->schemaManager->addSchema(new Row2Schema($header, $item));
			$this->crateGenerator = $container->create(ICrateGenerator::class);
			$this->crateDirectory->purge();
		}

		protected function tearDown() {
			$this->crateDirectory->delete();
		}
	}
