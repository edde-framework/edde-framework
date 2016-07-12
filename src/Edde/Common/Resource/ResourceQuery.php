<?php
	namespace Edde\Common\Resource;

	use Edde\Api\Resource\IResourceIndex;
	use Edde\Api\Resource\IResourceQuery;
	use Edde\Api\Schema\ISchema;
	use Edde\Common\Query\Select\SelectQuery;
	use Edde\Common\Usable\AbstractUsable;

	class ResourceQuery extends AbstractUsable implements IResourceQuery {
		/**
		 * @var IResourceIndex
		 */
		protected $resourceManager;
		/**
		 * @var ISchema
		 */
		protected $schema;
		/**
		 * @var SelectQuery
		 */
		protected $selectQuery;
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * schema should be resource schema
		 *
		 * @param IResourceIndex $resourceManager
		 * @param ISchema $schema
		 */
		public function __construct(IResourceIndex $resourceManager, ISchema $schema) {
			$this->resourceManager = $resourceManager;
			$this->schema = $schema;
		}

		public function getQuery() {
			$this->usse();
			return $this->selectQuery;
		}

		public function name($name) {
			$this->usse();
			$this->selectQuery->where()
				->eq()
				->property('name')
				->parameter($name);
			return $this;
		}

		public function nameLike($name) {
			$this->usse();
			$this->selectQuery->where()
				->like()
				->property('name')
				->parameter($name);
			return $this;
		}

		public function url($url) {
			$this->usse();
			$this->selectQuery->where()
				->eq()
				->property('url')
				->parameter($url);
			return $this;
		}

		public function urlLike($url) {
			$this->usse();
			$this->selectQuery->where()
				->like()
				->property('url')
				->parameter($url);
			return $this;
		}

		public function resource() {
			return $this->resourceManager->getResource($this);
		}

		public function collection() {
			return $this->resourceManager->getResourceCollection($this);
		}

		protected function prepare() {
			$this->selectQuery = new SelectQuery();
			$select = $this->selectQuery->select();
			foreach ($this->schema->getPropertyList() as $property) {
				$select->property($property->getName());
			}
			$this->selectQuery->select()
				->from()
				->source($this->schema->getSchemaName());
		}
	}
