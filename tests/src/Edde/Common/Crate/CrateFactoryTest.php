<?php
	namespace Edde\Common\Crate;

	use Edde\Api\Container\IContainer;
	use Edde\Common\Schema\Property;
	use Edde\Common\Schema\Schema;
	use Edde\Ext\Container\ContainerFactory;
	use Foo\Bar\Header;
	use Foo\Bar\Row;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets.php');

	class CrateFactoryTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;

		public function testCommon() {
			$crateFactory = new CrateFactory($this->container);

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

			$crateList = $crateFactory->build($source);
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
