<?php
	namespace Foo\Bar;

	use Edde\Common\Crate\Crate;
	use Edde\Common\Schema\Schema;
	use Edde\Common\Schema\SchemaProperty;

	class FooBarBar extends Crate {
	}

	class Header extends Crate {
	}

	class HeaderSchema extends Schema {
		public function __construct() {
			parent::__construct(Header::class);
		}

		protected function prepare() {
			$this->addPropertyList([
				(new SchemaProperty($this, 'guid'))->unique()
					->identifier()
					->required(),
				new SchemaProperty($this, 'name'),
			]);
		}
	}

	class Row extends Crate {
	}

	class RowSchema extends Schema {
		/**
		 * @var HeaderSchema
		 */
		protected $headerSchema;
		/**
		 * @var ItemSchema
		 */
		protected $itemSchema;

		/**
		 * @param HeaderSchema $headerSchema
		 * @param ItemSchema $itemSchema
		 */
		public function __construct(HeaderSchema $headerSchema, ItemSchema $itemSchema) {
			parent::__construct(Row::class);
			$this->headerSchema = $headerSchema;
			$this->itemSchema = $itemSchema;
		}

		protected function prepare() {
			$this->addPropertyList([
				(new SchemaProperty($this, 'guid'))->unique()
					->identifier()
					->required(),
				$rowHeaderProperty = (new SchemaProperty($this, 'header'))->required(),
				$rowItemProperty = new SchemaProperty($this, 'item'),
				new SchemaProperty($this, 'name'),
				new SchemaProperty($this, 'value'),
			]);
			$this->linkTo('header', 'rowCollection', $rowHeaderProperty, $this->headerSchema->getProperty('guid'));
			$this->linkTo('item', 'rowItemCollection', $rowItemProperty, $this->itemSchema->getProperty('guid'));
		}
	}

	class Item extends Crate {
	}

	class ItemSchema extends Schema {
		public function __construct() {
			parent::__construct(Item::class);
		}

		protected function prepare() {
			$this->addPropertyList([
				(new SchemaProperty($this, 'guid'))->identifier()
					->unique()
					->required(),
				new SchemaProperty($this, 'name'),
			]);
		}
	}
