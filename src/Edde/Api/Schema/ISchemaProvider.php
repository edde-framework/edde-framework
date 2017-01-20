<?php
	declare(strict_types=1);

	namespace Edde\Api\Schema;

	use Edde\Api\Node\INode;
	use IteratorAggregate;
	use Traversable;

	/**
	 * Schema provider should provide schema definition in node.
	 */
	interface ISchemaProvider extends IteratorAggregate {
		/**
		 * @return INode[]|Traversable
		 */
		public function getIterator(): Traversable;
	}
