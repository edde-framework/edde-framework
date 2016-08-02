<?php
	namespace Edde\Common\Schema;

	use phpunit\framework\TestCase;

	class SchemaTest extends TestCase {
		public function testCommon() {
			$schema = new Schema(self::class);
			self::assertSame('SchemaTest', $schema->getName());
			self::assertSame(__NAMESPACE__, $schema->getNamespace());
			self::assertSame(self::class, $schema->getSchemaName());
			self::assertFalse($schema->isUsed());
			self::assertEmpty($schema->getPropertyList());
			self::assertTrue($schema->isUsed());
		}

		public function testCommonProperty() {
			$schema = new Schema(self::class);
			$schema->addProperty((new SchemaProperty($schema, 'guid'))->required()
				->unique()
				->identifier());
		}
	}
