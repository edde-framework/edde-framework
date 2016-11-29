<?php
	declare(strict_types = 1);

	namespace Edde\Common;

	use Edde\Api\Deffered\DefferedException;
	use Edde\Api\EddeException;
	use PHPUnit\Framework\TestCase;

	/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
	class TestObject extends AbstractObject {
		public $foo;
		protected $bar;
		private $fooBar;
		public $prepared = false;

		public function takeAction() {
			$this->use();
			$this->prepared = true;
		}
	}

	class AbstractObjectTest extends TestCase {
		public function testObjectWrite() {
			$this->expectException(EddeException::class);
			$this->expectExceptionMessage('Writing to the undefined/private/protected property [Edde\Common\TestObject::$boo].');
			$object = new TestObject();
			$object->boo = true;
		}

		public function testObjectIsset() {
			$this->expectException(EddeException::class);
			$this->expectExceptionMessage('Cannot check isset on undefined/private/protected property [Edde\Common\TestObject::$boo].');
			$object = new TestObject();
			$isset = isset($object->boo);
		}

		public function testObjectRead() {
			$this->expectException(EddeException::class);
			$this->expectExceptionMessage('Reading from the undefined/private/protected property [Edde\Common\TestObject::$boo].');
			$object = new TestObject();
			$foo = $object->boo;
		}

		public function testPropertyCallback() {
			$object = new TestObject();
			$object->objectProperty('foo', function () {
				return 'bar';
			});
			$object->objectProperty('bar', function () {
				return 'foo';
			});
			$object->objectProperty('fooBar', function () {
				return 'fooBar';
			});
			self::assertTrue(isset($object->foo), 'Foo is not set.');
			self::assertTrue(isset($object->bar), 'Bar is not set.');
			self::assertTrue(isset($object->fooBar), 'FooBar is not set.');
			self::assertEquals('bar', $object->foo);
			self::assertEquals('foo', $object->bar);
			self::assertEquals('fooBar', $object->fooBar);
		}

		public function testUsableObject() {
			$object = new TestObject();
			$onDefferedFlag = false;
			self::assertFalse($object->prepared);
			self::assertFalse($object->isUsed());
			$object->registerOnUse(function () use (&$onDefferedFlag) {
				$onDefferedFlag = true;
			});
			$object->takeAction();
			self::assertTrue($onDefferedFlag);
			self::assertTrue($object->prepared);
			self::assertTrue($object->isUsed());
		}

		public function testSetupHook() {
			$object = new TestObject();
			$onDefferedFlag = false;
			$object->registerOnUse(function () use (&$onDefferedFlag) {
				$onDefferedFlag = true;
			});
			self::assertFalse($onDefferedFlag);
			$object->takeAction();
			self::assertTrue($onDefferedFlag);
		}

		public function testAfterDeffered() {
			$this->expectException(DefferedException::class);
			$this->expectExceptionMessage('Cannot add Edde\Common\TestObject::registerOnUse() callback to already used class [Edde\Common\TestObject].');
			$object = new TestObject();
			$object->takeAction();
			$object->registerOnUse(function () {
			});
		}
	}
