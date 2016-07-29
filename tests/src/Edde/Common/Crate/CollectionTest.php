<?php
	namespace Edde\Common\Crate;

	use Edde\Api\Container\IContainer;
	use Edde\Common\Schema\Property;
	use Edde\Common\Schema\Schema;
	use Edde\Ext\Container\ContainerFactory;
	use Foo\Bar\FooBarBar;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets.php');

	class CollectionTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;

		public function testCommon() {
			$schema = new Schema('Foo\\Bar\\FooBar');
			$schema->addProperty(new Property($schema, 'guid'));
			$schema->addProperty(new Property($schema, 'name'));
			$schema->addProperty(new Property($schema, 'long-name'));

			$collection = new Collection($this->container, $schema);
			self::assertEmpty(iterator_to_array($collection));
			$crate = $collection->createCrate();
			self::assertEmpty(iterator_to_array($collection));
			$collection->addCrate($crate);
			self::assertNotEmpty($collectionArray = iterator_to_array($collection));
			self::assertCount(1, $collectionArray);
			$crate->set('guid', '12345');
			self::assertEquals('12345', $crate->get('guid'));
			self::assertInstanceOf(Crate::class, $crate);
		}

		public function testInstanceCrate() {
			$schema = new Schema('Foo\\Bar\\FooBarBar');
			$schema->addProperty(new Property($schema, 'guid'));
			$schema->addProperty(new Property($schema, 'name'));
			$schema->addProperty(new Property($schema, 'long-name'));

			$collection = new Collection($this->container, $schema);
			$crate = $collection->createCrate();
			self::assertInstanceOf(FooBarBar::class, $crate);
		}

		protected function setUp() {
			$this->container = ContainerFactory::create([
				FooBarBar::class => FooBarBar::class,
			]);
		}
	}
