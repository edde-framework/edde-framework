<?php
	namespace Edde\Common\Resource;

	use Edde\Api\Resource\IResourceQuery;
	use Edde\Common\AbstractObject;

	class ResourceQuery extends AbstractObject implements IResourceQuery {
		/**
		 * @var string
		 */
		protected $name;

		public function name($name) {
			$this->name = $name;
			return $this;
		}
	}
