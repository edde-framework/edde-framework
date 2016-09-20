<?php
	declare(strict_types = 1);

	namespace Edde\Common\Schema;

	use phpunit\framework\TestCase;

	class SchemaPropertyTest extends TestCase {
		public function testDirty() {
			$schema = new Schema('dummy');
			$property = new SchemaProperty($schema, 'foo', 'int');
			self::assertFalse($property->isDirty('1', 1), 'string integer is dirty!');
			$property = new SchemaProperty($schema, 'foo', 'float');
			self::assertFalse($property->isDirty(3.141592, 3.141592), 'float is dirty!');
			self::assertTrue($property->isDirty(3.141592, 3.141593), 'float is NOT dirty!');
		}
	}
