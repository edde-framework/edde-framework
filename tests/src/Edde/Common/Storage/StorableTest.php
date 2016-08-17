<?php
	declare(strict_types = 1);

	namespace Edde\Common\Storage;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Storage\IStorage;
	use Edde\Common\Cache\CacheFactory;
	use Edde\Common\Crate\Crate;
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
		/**
		 * @var SqliteDriver
		 */
		protected $sqliteDriver;

		public function testSimpleStorable() {
			$crate = new Crate($this->container);
			$crate->setSchema($schema = $this->schemaManager->getSchema('Foo\\Bar\\SimpleStorable'));
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
				->source($schema->getSchemaName())
				->where()
				->eq()
				->property('guid')
				->parameter($guid);

			$crate = $this->storage->load($schema, $query);
			self::assertEquals($guid, $crate->get('guid'));
			self::assertEquals('foobar', $crate->get('value'));
			$count = 0;
			foreach ($this->storage->collection($schema) as $crate) {
				$count++;
			}
			self::assertEquals(2, $count);
		}

		public function testComplexStorable() {
			$groupSchema = $this->schemaManager->getSchema('Group');
			$identitySchema = $this->schemaManager->getSchema('Identity');
			$identityGroupSchema = $this->schemaManager->getSchema('IdentityGroup');

			$this->storage->start();
			$this->storage->execute(new CreateSchemaQuery($groupSchema));
			$this->storage->execute(new CreateSchemaQuery($identitySchema));
			$this->storage->execute(new CreateSchemaQuery($identityGroupSchema));

			$rootGroup = new Crate($this->container);
			$rootGroup->setSchema($groupSchema);
			$rootGroup->put([
				'guid' => sha1(random_bytes(64)),
				'name' => 'root',
			]);
			$this->storage->store($rootGroup);

			$guestGroup = new Crate($this->container);
			$guestGroup->setSchema($groupSchema);
			$guestGroup->put([
				'guid' => sha1(random_bytes(64)),
				'name' => 'guest',
			]);
			$this->storage->store($guestGroup);

			$godIdentity = new Crate($this->container);
			$godIdentity->setSchema($identitySchema);
			$godIdentity->put([
				'guid' => sha1(random_bytes(64)),
				'name' => 'The God',
			]);
			$this->storage->store($godIdentity);

			$guestIdentity = new Crate($this->container);
			$guestIdentity->setSchema($identitySchema);
			$guestIdentity->put([
				'guid' => sha1(random_bytes(64)),
				'name' => "The God's Guest",
			]);
			$this->storage->store($guestIdentity);

			$identityGroup = new Crate($this->container);
			$identityGroup->setSchema($identityGroupSchema);
			$identityGroup->put([
				'guid' => sha1(random_bytes(64)),
				'identity' => $godIdentity->get('guid'),
				'group' => $rootGroup->get('guid'),
			]);
			$this->storage->store($identityGroup);

			$identityGroup = new Crate($this->container);
			$identityGroup->setSchema($identityGroupSchema);
			$identityGroup->put([
				'guid' => sha1(random_bytes(64)),
				'identity' => $godIdentity->get('guid'),
				'group' => $guestGroup->get('guid'),
			]);
			$this->storage->store($identityGroup);

			$identityGroup = new Crate($this->container);
			$identityGroup->setSchema($identityGroupSchema);
			$identityGroup->put([
				'guid' => sha1(random_bytes(64)),
				'identity' => $guestIdentity->get('guid'),
				'group' => $guestGroup->get('guid'),
			]);
			$this->storage->store($identityGroup);

			$groupList = [];
			foreach ($this->storage->collectionTo($godIdentity, $identityGroupSchema, 'identity', 'group') as $storable) {
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
			$this->storage = new DatabaseStorage($this->container, $this->sqliteDriver = new SqliteDriver('sqlite:' . $tempDirectory->filename('storage.sqlite')), new CacheFactory(__DIR__, new DevNullCacheStorage()));
		}

		protected function tearDown() {
			$this->sqliteDriver->close();
			$tempDirectory = new TempDirectory(__DIR__ . '/temp');
			$tempDirectory->purge();
		}
	}
