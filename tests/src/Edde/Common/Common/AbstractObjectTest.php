<?php
	namespace Edde\Common;

	use Edde\Api\EddeException;
	use phpunit\framework\TestCase;

	/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
	class TestObject extends AbstractObject {
	}

	class AbstractObjectTest extends TestCase {
		public function testObjectWrite() {
			$this->expectException(EddeException::class);
			$this->expectExceptionMessage('Writing to the undefined/private/protected property [Edde\Common\TestObject::$foo].');
			$object = new TestObject();
			$object->foo = true;
		}

		public function testObjectRead() {
			$this->expectException(EddeException::class);
			$this->expectExceptionMessage('Reading from the undefined/private/protected property [Edde\Common\TestObject::$foo].');
			$object = new TestObject();
			$foo = $object->foo;
		}
	}
