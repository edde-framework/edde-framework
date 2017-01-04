<?php
	declare(strict_types = 1);

	namespace Edde\Common;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\EddeException;
	use Edde\Test\FooObject;
	use PHPUnit\Framework\TestCase;

	require_once __DIR__ . '/assets/assets.php';

	class ObjectTest extends TestCase {
		/**
		 * @var FooObject
		 */
		protected $object;

		public function testInstanceOfLazyInject() {
			self::assertInstanceOf(ILazyInject::class, $this->object);
		}

		public function testWriteException() {
			$this->expectException(EddeException::class);
			$this->expectExceptionMessage('Writing to the undefined/private/protected property [Edde\Test\FooObject::$thisWillThrowAnException].');
			/** @noinspection PhpUndefinedFieldInspection */
			$this->object->thisWillThrowAnException = 'really!';
		}

		public function testReadException() {
			$this->expectException(EddeException::class);
			$this->expectExceptionMessage('Reading from the undefined/private/protected property [Edde\Test\FooObject::$yesThisWillThrowAnException].');
			/** @noinspection PhpUnusedLocalVariableInspection */
			/** @noinspection PhpUndefinedFieldInspection */
			$willYouThrowAnException = $this->object->yesThisWillThrowAnException;
		}

		public function testIsset() {
			self::assertFalse(isset($this->object->yesThisWillThrowAnException));
			self::assertTrue(isset($this->object->foo));
		}

		public function testObjectHash() {
			self::assertSame($this->object->hash(), $this->object->hash());
		}

		public function testSerializeHash() {
			$object = unserialize(serialize($this->object));
			self::assertSame($object->hash(), $this->object->hash());
		}

		protected function setUp() {
			$this->object = new FooObject();
		}
	}
