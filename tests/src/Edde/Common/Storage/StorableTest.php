<?php
	declare(strict_types = 1);

	namespace Edde\Common\Storage;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Storage\IStorage;
	use Edde\Common\Cache\CacheFactory;
	use Edde\Common\Database\DatabaseStorage;
	use Edde\Common\File\TempDirectory;
	use Edde\Common\Query\Schema\CreateSchemaQuery;
	use Edde\Common\Query\Select\SelectQuery;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Schema\SchemaFactory;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Ext\Cache\DevNullCacheStorage;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Database\Sqlite\SqliteDriver;
	use Edde\Ext\Resource\JsonResourceHandler;
	use phpunit\framework\TestCase;

	class StorableTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var ISchemaManager
		 */
		protected $schemaManager;
		/**
		 * @var IStorage
		 */
		protected $storage;

		public function testSimpleStorable() {
			$storable = new Storable($this->container);
			$storable->setSchema($schema = $this->schemaManager->getSchema('Foo\\Bar\\SimpleStorable'));
			$this->storage->start();
			$this->storage->execute(new CreateSchemaQuery($schema));

			$storable->set('guid', $guid = sha1(random_bytes(64)));
			$storable->set('value', 'foobar');
			$this->storage->store($storable);

			$storable->set('guid', sha1(random_bytes(64)));
			$storable->set('value', 'barfoo');
			$this->storage->store($storable);

			$this->storage->commit();

			$query = new SelectQuery();
			$query->select()
				->all()
				->from()
				->source($schema->getSchemaName())
				->where()
				->eq()
				->property('guid')
				->parameter($guid);

			$storable = $this->storage->storable($schema, $query);
			self::assertEquals($guid, $storable->get('guid'));
			self::assertEquals('foobar', $storable->get('value'));
			$count = 0;
			foreach ($this->storage->collection($schema) as $storable) {
				$count++;
			}
			self::assertEquals(2, $count);
		}

		protected function setUp() {
			$resourceManager = new ResourceManager();
			$resourceManager->registerResourceHandler(new JsonResourceHandler());
			$schemaFactory = new SchemaFactory($resourceManager);
			$schemaFactory->load(__DIR__ . '/assets/simple-storable.json');
			$this->schemaManager = new SchemaManager($schemaFactory);
			$this->container = ContainerFactory::create([
				Storable::class,
			]);
			$tempDirectory = new TempDirectory(__DIR__ . '/temp');
			$tempDirectory->purge();
			$this->storage = new DatabaseStorage($this->container, new SqliteDriver('sqlite:' . $tempDirectory->filename('storage.sqlite')), new CacheFactory(__DIR__, new DevNullCacheStorage()));
		}
	}
