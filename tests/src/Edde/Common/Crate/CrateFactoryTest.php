<?php
	declare(strict_types = 1);

	namespace Edde\Common\Crate;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crate\CrateException;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Common\Container\Factory\FactoryFactory;
	use Edde\Common\Resource\ResourceManager;
	use Edde\Common\Schema\SchemaFactory;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Ext\Container\ContainerFactory;
	use Foo\Bar\Header;
	use Foo\Bar\HeaderSchema;
	use Foo\Bar\Item;
	use Foo\Bar\ItemSchema;
	use Foo\Bar\Row;
	use Foo\Bar\RowSchema;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/assets.php');

	class CrateFactoryTest extends TestCase {
		/**
		 * @var ISchemaManager
		 */
		protected $schemaManager;
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var ICrateFactory
		 */
		protected $crateFactory;

		public function testCommon() {
			$source = [
				Header::class => [
					'guid' => 'header-guid',
					'name' => 'header name',
					'rowCollection' => [
						[
							'guid' => 'first guid',
							'name' => 'first name',
							'value' => 'first value',
						],
					],
				],
			];
			$crateList = $this->crateFactory->build($source);
			$header = reset($crateList);
			self::assertInstanceOf(Header::class, $header);
			self::assertEquals('header-guid', $header->get('guid'));
			self::assertCount(1, iterator_to_array($header->collection('rowCollection')));
			$crateList = [];
			foreach ($header->collection('rowCollection') as $crate) {
				$crateList[] = $crate;
			}
			self::assertCount(1, $crateList);
			$row = reset($crateList);
			self::assertInstanceOf(Row::class, $row);
			self::assertEquals('first guid', $row->get('guid'));
			self::assertEquals('first name', $row->get('name'));
		}

		public function testBadData() {
			$this->expectException(CrateException::class);
			$this->expectExceptionMessage('Cannot push source value into the crate [Foo\Bar\Header]; value [rowCollection] is not an array (collection).');
			$source = [
				Header::class => [
					'guid' => 'header-guid',
					'name' => 'header name',
					'rowCollection' => [
						'guid' => 'first guid',
						'name' => 'first name',
						'value' => 'first value',
					],
				],
			];
			$this->crateFactory->build($source);
		}

		public function testSingleMultiLink() {
			$source = [
				Header::class => [
					'guid' => 'header-guid',
					'name' => 'header name',
					'rowCollection' => [
						[
							'guid' => 'first guid',
							'name' => 'first name',
							'value' => 'first value',
							'item' => [
								'name' => 'whohooo!',
							],
						],
						[
							'guid' => 'second guid',
							'name' => 'second name',
							'value' => 'second value',
							'item' => [
								'name' => 'another whohooo!',
							],
						],
					],
				],
			];
			$crateList = $this->crateFactory->build($source);
			self::assertCount(1, $crateList);
			$header = reset($crateList);
			self::assertInstanceOf(Header::class, $header);
			self::assertEquals('header-guid', $header->get('guid'));
			self::assertEquals('header name', $header->get('name'));
			self::assertCount(2, iterator_to_array($header->collection('rowCollection')));
			$crateList = [];
			foreach ($header->collection('rowCollection') as $crate) {
				$crateList[] = $crate;
			}
			self::assertCount(2, $crateList);
			$firstRow = reset($crateList);
			$secondRow = end($crateList);

			self::assertInstanceOf(Row::class, $firstRow);
			self::assertEquals('first guid', $firstRow->get('guid'));
			self::assertEquals('first name', $firstRow->get('name'));
			self::assertInstanceOf(Item::class, $firstItem = $firstRow->link('item'));
			self::assertEquals('whohooo!', $firstItem->get('name'));

			self::assertInstanceOf(Row::class, $secondRow);
			self::assertEquals('second guid', $secondRow->get('guid'));
			self::assertEquals('second name', $secondRow->get('name'));
			self::assertInstanceOf(Item::class, $secondItem = $secondRow->link('item'));
			self::assertEquals('another whohooo!', $secondItem->get('name'));
		}

		protected function setUp() {
			$this->schemaManager = new SchemaManager(new SchemaFactory(new ResourceManager()));

			$rowSchema = new RowSchema($headerSchema = new HeaderSchema(), $itemSchema = new ItemSchema());

			$this->schemaManager->addSchema($headerSchema);
			$this->schemaManager->addSchema($rowSchema);
			$this->schemaManager->addSchema($itemSchema);
			$headerSchema->use();
			$rowSchema->use();
			$itemSchema->use();

			$this->container = ContainerFactory::create([
				Crate::class,
				Header::class,
				Row::class,
				Item::class,
				Collection::class,
			]);
			$this->container->registerFactory(ICrateFactory::class, FactoryFactory::create(ICrateFactory::class, $this->crateFactory = new CrateFactory($this->container, $this->schemaManager)));
		}
	}
