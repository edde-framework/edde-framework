<?php
	namespace Edde\Common\Storage;

	use Edde\Api\Schema\ISchema;
	use Edde\Api\Storage\IStorable;
	use Edde\Common\AbstractObject;

	class AbstractStorable extends AbstractObject implements IStorable {
		/**
		 * schema of this storable
		 *
		 * @var ISchema
		 */
		protected $schema;

		/**
		 * @param ISchema $schema
		 */
		public function __construct(ISchema $schema) {
			$this->schema = $schema;
		}

		public function getSchema() {
			return $this->schema;
		}
	}
