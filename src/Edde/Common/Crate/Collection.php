<?php
	namespace Edde\Common\Crate;

	use ArrayIterator;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Crate\CrateException;
	use Edde\Api\Crate\ICollection;
	use Edde\Api\Crate\ICrate;
	use Edde\Api\Schema\ISchema;
	use Edde\Common\Usable\AbstractUsable;

	class Collection extends AbstractUsable implements ICollection {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var ISchema
		 */
		protected $schema;
		/**
		 * crate class name (for a container)
		 *
		 * @var string
		 */
		protected $crate;
		/**
		 * @var ICrate[]
		 */
		protected $crateList = [];

		/**
		 * @param IContainer $container
		 * @param ISchema $schema
		 */
		public function __construct(IContainer $container, ISchema $schema) {
			$this->container = $container;
			$this->schema = $schema;
		}

		public function getSchema() {
			return $this->schema;
		}

		/**
		 * @return ICrate
		 */
		public function createCrate() {
			$this->usse();
			return $this->container->create($this->crate)
				->setSchema($this->schema);
		}

		public function addCrate(ICrate $crate) {
			if ($crate->getSchema() !== $this->schema) {
				throw new CrateException(sprintf('Cannot add crate with different schema [%s] to the collection [%s].', $crate->getSchema()
					->getSchemaName(), $this->schema->getSchemaName()));
			}
			$this->crateList[] = $crate;
			return $this;
		}

		public function getIterator() {
			return new ArrayIterator($this->crateList);
		}

		protected function prepare() {
			if ($this->container->has($this->crate = $this->schema->getSchemaName()) === false) {
				$this->crate = Crate::class;
			}
		}
	}
