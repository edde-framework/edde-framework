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

		public function testComplexStorable() {
			$groupSchema = $this->schemaManager->getSchema('Group');
			$identitySchema = $this->schemaManager->getSchema('Identity');
			$identityGroupSchema = $this->schemaManager->getSchema('IdentityGroup');

			$this->storage->start();
			$this->storage->execute(new CreateSchemaQuery($groupSchema));
			$this->storage->execute(new CreateSchemaQuery($identitySchema));
			$this->storage->execute(new CreateSchemaQuery($identityGroupSchema));

			$rootGroup = new Storable($this->container);
			$rootGroup->setSchema($groupSchema);
			$rootGroup->put([
				'guid' => sha1(random_bytes(64)),
				'name' => 'root',
			]);
			$this->storage->store($rootGroup);

			$guestGroup = new Storable($this->container);
			$guestGroup->setSchema($groupSchema);
			$guestGroup->put([
				'guid' => sha1(random_bytes(64)),
				'name' => 'guest',
			]);
			$this->storage->store($guestGroup);

			$godIdentity = new Storable($this->container);
			$godIdentity->setSchema($identitySchema);
			$godIdentity->put([
				'guid' => sha1(random_bytes(64)),
				'name' => 'The God',
			]);
			$this->storage->store($godIdentity);

			$guestIdentity = new Storable($this->container);
			$guestIdentity->setSchema($identitySchema);
			$guestIdentity->put([
				'guid' => sha1(random_bytes(64)),
				'name' => "The God's Guest",
			]);
			$this->storage->store($guestIdentity);

			$identityGroup = new Storable($this->container);
			$identityGroup->setSchema($identityGroupSchema);
			$identityGroup->put([
				'guid' => sha1(random_bytes(64)),
				'identity' => $godIdentity->get('guid'),
				'group' => $rootGroup->get('guid'),
			]);
			$this->storage->store($identityGroup);

			$identityGroup = new Storable($this->container);
			$identityGroup->setSchema($identityGroupSchema);
			$identityGroup->put([
				'guid' => sha1(random_bytes(64)),
				'identity' => $godIdentity->get('guid'),
				'group' => $guestGroup->get('guid'),
			]);
			$this->storage->store($identityGroup);

			$identityGroup = new Storable($this->container);
			$identityGroup->setSchema($identityGroupSchema);
			$identityGroup->put([
				'guid' => sha1(random_bytes(64)),
				'identity' => $guestIdentity->get('guid'),
				'group' => $guestGroup->get('guid'),
			]);
			$this->storage->store($identityGroup);

			$this->storage->commit();

			$groupList = [];
			foreach ($this->storage->collectionTo($godIdentity, $identityGroupSchema, 'identity', 'group') as $storable) {
				$groupList[] = $storable->get('name');
			}
			self::assertEquals([
				'root',
				'guest',
			], $groupList);
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
				Storable::class,
			]);
			$tempDirectory = new TempDirectory(__DIR__ . '/temp');
			$tempDirectory->purge();
			$this->storage = new DatabaseStorage($this->container, new SqliteDriver('sqlite:' . $tempDirectory->filename('storage.sqlite')), new CacheFactory(__DIR__, new DevNullCacheStorage()));
		}
	}
