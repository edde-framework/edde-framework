<?php
	namespace Edde\Common\Schema;

	use Edde\Api\Schema\ISchemaFactory;
	use Edde\Common\Node\Node;
	use phpunit\framework\TestCase;

	class SchemaFactoryTest extends TestCase {
		/**
		 * @var ISchemaFactory
		 */
		protected $schemaFactory;

		public function testCommon() {
			$this->schemaFactory->addSchemaNode($haderSchemaNode = (new Node('Header', null, ['namespace' => 'Foo\\Bar']))->addNodeList([
				(new Node('property-list'))->addNodeList([
					new Node('guid', null, [
						'unique' => true,
						'required' => true,
						'identifier' => true,
					]),
					new Node('name', null, [
						'required' => true,
					]),
				]),
				(new Node('collection'))->addNodeList([
					new Node('rowCollection', 'guid', [
						'schema' => 'Foo\\Bar\\Row',
						'property' => 'header',
					]),
				]),
			]));
			$this->schemaFactory->addSchemaNode($rowSchemaNode = (new Node('Row', null, ['namespace' => 'Foo\\Bar']))->addNodeList([
				(new Node('property-list'))->addNodeList([
					new Node('guid', null, [
						'unique' => true,
						'required' => true,
						'identifier' => true,
					]),
					new Node('header', null, [
						'required' => true,
					]),
					new Node('name', null, [
						'required' => true,
					]),
				]),
				(new Node('link'))->addNodeList([
					new Node('header', 'header', [
						'schema' => 'Foo\\Bar\\Header',
						'property' => 'guid',
					]),
				]),
			]));

			$schemaList = $this->schemaFactory->create();
			self::assertArrayHasKey('Foo\\Bar\\Header', $schemaList);
			self::assertArrayHasKey('Foo\\Bar\\Row', $schemaList);

			$headerSchema = $schemaList['Foo\\Bar\\Header'];
			self::assertTrue($headerSchema->hasCollection('rowCollection'));
			self::assertEquals([
				'guid',
				'name',
			], array_keys($headerSchema->getPropertyList()));

			$rowSchema = $schemaList['Foo\\Bar\\Row'];
			self::assertTrue($rowSchema->hasLink('header'));
			self::assertEquals([
				'guid',
				'header',
				'name',
			], array_keys($rowSchema->getPropertyList()));
		}

		protected function setUp() {
			$this->schemaFactory = new SchemaFactory();
		}
	}
