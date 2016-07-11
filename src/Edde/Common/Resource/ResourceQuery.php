<?php
	namespace Edde\Common\Resource;

	use Edde\Api\Query\IQuery;
	use Edde\Api\Resource\IResourceQuery;
	use Edde\Api\Schema\ISchema;
	use Edde\Common\Query\Select\SelectQuery;
	use Edde\Common\Usable\AbstractUsable;

	class ResourceQuery extends AbstractUsable implements IResourceQuery {
		/**
		 * @var ISchema
		 */
		protected $schema;
		/**
		 * @var IQuery
		 */
		protected $query;
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * schema should be resource schema
		 *
		 * @param ISchema $schema
		 */
		public function __construct(ISchema $schema) {
			$this->schema = $schema;
		}

		public function getQuery() {
			$this->usse();
			return $this->query;
		}

		public function name($name) {
			$this->name = $name;
			return $this;
		}

		protected function prepare() {
			$this->query = new SelectQuery();
		}
	}
