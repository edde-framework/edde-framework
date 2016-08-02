<?php
	namespace Edde\Common\Crate;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crate\ICrate;
	use Edde\Common\Schema\Schema;
	use Edde\Common\Schema\SchemaProperty;
	use Edde\Ext\Container\ContainerFactory;
	use Foo\Bar\Header;
	use Foo\Bar\Row;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets.php');

	class CrateTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;

		public function testLinks() {
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
			/** @var $headerCrate ICrate */
			$headerCrate = $this->container->create(Header::class);
			self::assertInstanceOf(Header::class, $headerCrate);
			$headerCrate->setSchema($headerSchema);
			$rowCollection = $headerCrate->collection('rowCollection');
			self::assertInstanceOf(Row::class, $rowCrate = $rowCollection->createCrate());
		}

		protected function setUp() {
			$this->container = ContainerFactory::create([
				Crate::class,
				Header::class,
				Row::class,
				Collection::class,
			]);
		}
	}
