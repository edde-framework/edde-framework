<?php
	namespace Edde\Common\Crate;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crate\ICrate;
	use Edde\Common\Schema\Property;
	use Edde\Common\Schema\Schema;
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
			$headerSchema = new Schema('Foo\\Bar\\Header');
			$headerSchema->addPropertyList([
				$headerGuid = new Property($headerSchema, 'guid', null, true, true, true),
				new Property($headerSchema, 'name'),
			]);
			$rowSchema = new Schema('Foo\\Bar\\Row');
			$rowSchema->addPropertyList([
				new Property($rowSchema, 'guid', null, true, true, true),
				$headerLink = new Property($rowSchema, 'header', null, true, false, false),
				new Property($rowSchema, 'name'),
				new Property($rowSchema, 'value'),
			]);
			$headerGuid->link($headerLink, 'rowCollection');
			self::assertTrue($headerGuid->isLink());
			self::assertFalse($headerLink->isLink());
			self::assertNotEmpty($headerSchema->getLinkList());
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
