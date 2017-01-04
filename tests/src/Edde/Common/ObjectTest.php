<?php
	declare(strict_types = 1);

	namespace Edde\Common;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\EddeException;
	use Edde\Common\Serialize\HashIndex;
	use Edde\Test\BarObject;
	use Edde\Test\CompositeObject;
	use Edde\Test\FooObject;
	use PHPUnit\Framework\TestCase;

	require_once __DIR__ . '/assets/assets.php';

	class ObjectTest extends TestCase {
		/**
		 * @var FooObject
		 */
		protected $fooObject;
		/**
		 * @var BarObject
		 */
		protected $barObject;
		/**
		 * @var CompositeObject
		 */
		protected $composite;

		public function testInstanceOfLazyInject() {
			self::assertInstanceOf(ILazyInject::class, $this->fooObject);
		}

		public function testWriteException() {
			$this->expectException(EddeException::class);
			$this->expectExceptionMessage('Writing to the undefined/private/protected property [Edde\Test\FooObject::$thisWillThrowAnException].');
			/** @noinspection PhpUndefinedFieldInspection */
			$this->fooObject->thisWillThrowAnException = 'really!';
		}

		public function testReadException() {
			$this->expectException(EddeException::class);
			$this->expectExceptionMessage('Reading from the undefined/private/protected property [Edde\Test\FooObject::$yesThisWillThrowAnException].');
			/** @noinspection PhpUnusedLocalVariableInspection */
			/** @noinspection PhpUndefinedFieldInspection */
			$willYouThrowAnException = $this->fooObject->yesThisWillThrowAnException;
		}

		public function testIsset() {
			self::assertFalse(isset($this->fooObject->yesThisWillThrowAnException));
			self::assertTrue(isset($this->fooObject->foo));
		}

		public function testObjectHash() {
			self::assertSame($this->fooObject->hash(), $this->fooObject->hash());
		}

		public function testSerializeHash() {
			$object = unserialize(serialize($this->fooObject));
			self::assertSame($object->hash(), $this->fooObject->hash());
		}

		public function testHashIndex() {
			$hash = HashIndex::serialize();
			serialize($this->fooObject);
			self::assertNotSame($hash, $hashIndex = HashIndex::serialize());
			self::assertSame($this->fooObject, HashIndex::load($this->fooObject->hash()));
			HashIndex::unserialize($hashIndex);
			self::assertEquals($this->fooObject, HashIndex::load($this->fooObject->hash()));
		}

		public function testComposite() {
			self::assertSame($this->composite->getBar()
				->getFoo(), $this->composite->getFoo());
			self::assertEmpty(HashIndex::getIndex());
			$foo = serialize($this->composite->getFoo());
			$composite = serialize($this->composite);
			self::assertNotEmpty(HashIndex::getIndex());
			self::assertCount(3, HashIndex::getIndex());
			HashIndex::drop();
			self::assertEmpty(HashIndex::getIndex());
			$foo = unserialize($foo);
			$composite = unserialize($composite);
			self::assertNotEmpty(HashIndex::getIndex());
			self::assertCount(3, HashIndex::getIndex());
			self::assertEquals($cfoo = $this->composite->getFoo(), HashIndex::load($cfoo->hash()));
			self::assertSame($foo, $composite->getFoo());
		}

		protected function setUp() {
			$this->composite = new CompositeObject($this->fooObject = new FooObject(), $this->barObject = new BarObject($this->fooObject));
		}
	}
