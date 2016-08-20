<?php
	declare(strict_types = 1);

	namespace Edde\Common\Storage;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Storage\IStorage;
	use Edde\Common\Cache\CacheFactory;
	use Edde\Common\Container\Factory\FactoryFactory;
	use Edde\Common\Crate\Crate;
	use Edde\Common\Crate\CrateFactory;
	use Edde\Common\Crate\DummyCrateGenerator;
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

	class StorageTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var ISchemaManager
		 */
		protected $schemaManager;
		/**
		 * @var ICrateFactory
		 */
		protected $crateFactory;
		/**
		 * @var IStorage
		 */
		protected $storage;
		/**
		 * @var SqliteDriver
		 */
		protected $sqliteDriver;

		public function testSimpleStorable() {
			$crate = $this->crateFactory->crate(Crate::class, null, 'Foo\\Bar\\SimpleStorable');
			$schema = $this->schemaManager->getSchema('Foo\\Bar\\SimpleStorable');
			$this->storage->start();
			$this->storage->execute(new CreateSchemaQuery($schema));

			$crate->set('guid', $guid = sha1(random_bytes(64)));
			$crate->set('value', 'foobar');
			$this->storage->store($crate);

			$crate->set('guid', sha1(random_bytes(64)));
			$crate->set('value', 'barfoo');
			$this->storage->store($crate);

			$this->storage->commit();

			$query = new SelectQuery();
			$query->select()
				->all()
				->from()
				->source($schemaName = $schema->getSchemaName())
				->where()
				->eq()
				->property('guid')
				->parameter($guid);

			$crate = $this->storage->load(Crate::class, $query, $schemaName);
			self::assertEquals($guid, $crate->get('guid'));
			self::assertEquals('foobar', $crate->get('value'));
			$count = 0;
			foreach ($this->storage->collection(Crate::class, null, $schemaName) as $crate) {
				$count++;
			}
			self::assertEquals(2, $count);
		}

		public function testComplexStorable() {
			$this->storage->start();
			$this->storage->execute(new CreateSchemaQuery($this->schemaManager->getSchema('Group')));
			$this->storage->execute(new CreateSchemaQuery($this->schemaManager->getSchema('Identity')));
			$this->storage->execute(new CreateSchemaQuery($this->schemaManager->getSchema('IdentityGroup')));

			$this->storage->store($rootGroup = $this->crateFactory->crate(Crate::class, null, 'Group')
				->put([
					'guid' => sha1(random_bytes(64)),
					'name' => 'root',
				]))
				->store($guestGroup = $this->crateFactory->crate(Crate::class, null, 'Group')
					->put([
						'guid' => sha1(random_bytes(64)),
						'name' => 'guest',
					]))
				->store($godIdentity = $this->crateFactory->crate(Crate::class, null, 'Identity')
					->put([
						'guid' => sha1(random_bytes(64)),
						'name' => 'The God',
					]))
				->store($guestIdentity = $this->crateFactory->crate(Crate::class, null, 'Identity')
					->put([
						'guid' => sha1(random_bytes(64)),
						'name' => "The God's Guest",
					]))
				->store($rootGod = $this->crateFactory->crate(Crate::class, null, 'IdentityGroup')
					->put([
						'guid' => sha1(random_bytes(64)),
					])
					->linkTo([
						'identity' => $godIdentity,
						'group' => $rootGroup,
					]))
				->store($guestGod = $this->crateFactory->crate(Crate::class, null, 'IdentityGroup')
					->put([
						'guid' => sha1(random_bytes(64)),
					])
					->linkTo([
						'identity' => $godIdentity,
						'group' => $guestGroup,
					]))
				->store($guestGuest = $this->crateFactory->crate(Crate::class, null, 'IdentityGroup')
					->put([
						'guid' => sha1(random_bytes(64)),
					])
					->linkTo([
						'identity' => $guestIdentity,
						'group' => $guestGroup,
					]));

			$groupList = [];
			foreach ($this->storage->collectionTo($godIdentity, 'IdentityGroup', 'identity', 'group', Crate::class) as $storable) {
				$groupList[] = $storable->get('name');
			}
			self::assertEquals([
				'root',
				'guest',
			], $groupList);

			$this->storage->commit();
		}

		protected function setUp() {
			$resourceManager = new ResourceManager();
			$resourceManager->registerResourceHandler(new JsonResourceHandler());
			$schemaFactory = new SchemaFactory($resourceManager);
			$schemaFactory->load(__DIR__ . '/assets/simple-storable.json');
			$schemaFactory->load(__DIR__ . '/assets/identity-storable.json');
			$schemaFactory->load(__DIR__ . '/assets/group-storable.json');
			$schemaFactory->load(__DIR__ . '/assets/identity-group-storable.json');
			$this->schemaManager = new SchemaManager($schemaFactory);
			$this->container = ContainerFactory::create([
				Crate::class,
			]);
			$tempDirectory = new TempDirectory(__DIR__ . '/temp');
			$tempDirectory->purge();
			$this->storage = new DatabaseStorage($this->sqliteDriver = new SqliteDriver('sqlite:' . $tempDirectory->filename('storage.sqlite')), new CacheFactory(__DIR__, new DevNullCacheStorage()));
			$this->storage->lazySchemaManager($this->schemaManager);
			$this->container->registerFactory(ICrateFactory::class, FactoryFactory::create(ICrateFactory::class, $this->crateFactory = new CrateFactory($this->container, $this->schemaManager, new DummyCrateGenerator())));
			$this->storage->lazyCrateFactory($this->crateFactory);
		}

		protected function tearDown() {
			$this->sqliteDriver->close();
			$tempDirectory = new TempDirectory(__DIR__ . '/temp');
			$tempDirectory->purge();
		}
	}
