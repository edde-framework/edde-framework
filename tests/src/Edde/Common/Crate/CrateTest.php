<?php
	declare(strict_types = 1);

	namespace Edde\Common\Crate;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crate\CrateException;
	use Edde\Api\Crate\ICrate;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Common\Container\Factory\FactoryFactory;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Schema\Schema;
	use Edde\Common\Schema\SchemaFactory;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Common\Schema\SchemaProperty;
	use Edde\Ext\Container\ContainerFactory;
	use Foo\Bar\Header;
	use Foo\Bar\Row;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/assets.php');

	class CrateTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var ICrateFactory
		 */
		protected $crateFactory;
		/**
		 * @var ISchemaManager
		 */
		protected $schemaManager;

		public function testLinks() {
			$headerSchema = $this->schemaManager->getSchema(Header::class);
			$rowSchema = $this->schemaManager->getSchema(Row::class);

			self::assertTrue($rowSchema->hasLink('header'));
			self::assertTrue($headerSchema->hasCollection('rowCollection'));

			/** @var $headerCrate ICrate */
			$headerCrate = $this->container->create(Header::class);
			self::assertInstanceOf(Header::class, $headerCrate);
			$headerCrate->setSchema($headerSchema);
		}

		public function testArraysException() {
			$this->expectException(CrateException::class);
			$this->expectExceptionMessage('Property [schema::hello] is not array; cannot add value.');
			$crate = new Crate($this->crateFactory);
			$schema = new Schema('schema');
			$crate->addProperty(new Property(new SchemaProperty($schema, 'hello')));
			$crate->add('hello', false);
		}

		public function testArrays() {
			$crate = new Crate($this->crateFactory);
			$schema = new Schema('schema');
			$crate->addProperty(new Property(new SchemaProperty($schema, 'hello', 'string', false, false, false, true)));
			$crate->add('hello', 'hello');
			$crate->add('hello', 'bello');
			$crate->add('hello', 'whepee!', 'key');
			self::assertEquals([
				0 => 'hello',
				1 => 'bello',
				'key' => 'whepee!',
			], $crate->get('hello'));
		}

		protected function setUp() {
			$this->container = ContainerFactory::create([
				Crate::class,
				Header::class,
				Row::class,
				Collection::class,
			]);
			$this->container->registerFactory(ICrateFactory::class, FactoryFactory::create(ICrateFactory::class, $this->crateFactory = new CrateFactory($this->container, $this->schemaManager = $schemaManager = new SchemaManager(new SchemaFactory(new ResourceManager())), new DummyCrateGenerator())));
			$headerSchema = new Schema(Header::class);
			$headerSchema->addPropertyList([
				$headerGuid = (new SchemaProperty($headerSchema, 'guid'))->unique()
					->required()
					->identifier(),
				new SchemaProperty($headerSchema, 'name'),
			]);
			$rowSchema = new Schema(Row::class);
			$rowSchema->addPropertyList([
				(new SchemaProperty($rowSchema, 'guid'))->unique()
					->identifier()
					->required(),
				$headerLink = (new SchemaProperty($rowSchema, 'header'))->required(),
				new SchemaProperty($rowSchema, 'name'),
				new SchemaProperty($rowSchema, 'value'),
			]);

			$rowSchema->linkTo('header', 'rowCollection', $headerLink, $headerGuid);
			$schemaManager->addSchema($headerSchema);
			$schemaManager->addSchema($rowSchema);
		}
	}
