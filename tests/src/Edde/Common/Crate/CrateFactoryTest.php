<?php
	namespace Edde\Common\Crate;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crate\CrateException;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Common\Schema\Schema;
	use Edde\Common\Schema\SchemaManager;
	use Edde\Common\Schema\SchemaProperty;
	use Edde\Ext\Container\ContainerFactory;
	use Foo\Bar\Header;
	use Foo\Bar\Item;
	use Foo\Bar\Row;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets.php');

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
			self::assertEquals($header->get('guid'), 'header-guid');
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
			$headerCrate = $this->crateFactory->build($source);
		}

		protected function setUp() {
			$this->schemaManager = new SchemaManager();

			$headerSchema = new Schema(Header::class);
			$headerSchema->addPropertyList([
				$headerGuidProperty = new SchemaProperty($headerSchema, 'guid', null, true, true, true),
				new SchemaProperty($headerSchema, 'name'),
			]);
			$rowSchema = new Schema(Row::class);
			$rowSchema->addPropertyList([
				new SchemaProperty($rowSchema, 'guid', null, true, true, true),
				$rowHeaderProperty = new SchemaProperty($rowSchema, 'header', null, true, false, false),
				$rowItemProperty = new SchemaProperty($rowSchema, 'item', null, false, false, false),
				new SchemaProperty($rowSchema, 'name'),
				new SchemaProperty($rowSchema, 'value'),
			]);
			$itemSchema = new Schema(Item::class);
			$itemSchema->addPropertyList([
				$itemGuidProperty = new SchemaProperty($itemSchema, 'guid', null, true, true, true),
				new SchemaProperty($itemSchema, 'name'),
			]);
			$headerGuidProperty->link($rowHeaderProperty, 'rowCollection');
			$rowItemProperty->link($itemGuidProperty, 'item');

			$this->schemaManager->addSchema($headerSchema);
			$this->schemaManager->addSchema($rowSchema);
			$this->schemaManager->addSchema($itemSchema);

			$this->container = ContainerFactory::create([
				Crate::class,
				Header::class,
				Row::class,
				Collection::class,
			]);

			$this->crateFactory = new CrateFactory($this->container, $this->schemaManager);
		}
	}
