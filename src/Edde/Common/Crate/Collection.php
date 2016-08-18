<?php
	declare(strict_types = 1);

	namespace Edde\Common\Crate;

	use ArrayIterator;
	use Edde\Api\Crate\CrateException;
	use Edde\Api\Crate\ICollection;
	use Edde\Api\Crate\ICrate;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Schema\ISchema;
	use Edde\Common\AbstractObject;

	class Collection extends AbstractObject implements ICollection {
		/**
		 * @var ICrateFactory
		 */
		protected $crateFactory;
		/**
		 * @var ISchema
		 */
		protected $schema;
		protected $crate;
		/**
		 * @var ICrate[]
		 */
		protected $crateList = [];

		/**
		 * @param ICrateFactory $crateFactory
		 * @param string $schema
		 * @param string $crate
		 */
		public function __construct(ICrateFactory $crateFactory, string $schema, string $crate = null) {
			$this->crateFactory = $crateFactory;
			$this->schema = $schema;
			$this->crate = $crate;
		}

		public function getSchema() {
			return $this->schema;
		}

		/**
		 * @return ICrate
		 */
		public function createCrate() {
			return $this->crateFactory->crate($this->crate ?: $this->schema, null, $this->schema);
		}

		public function addCrate(ICrate $crate) {
			$schema = $crate->getSchema();
			if ($schema->getSchemaName() !== $this->schema) {
				throw new CrateException(sprintf('Cannot add crate with different schema [%s] to the collection [%s].', $crate->getSchema()
					->getSchemaName(), $this->schema));
			}
			$this->crateList[] = $crate;
			return $this;
		}

		public function getIterator() {
			return new ArrayIterator($this->crateList);
		}
	}
