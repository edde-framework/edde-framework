<?php
	namespace Edde\Common\Crate;

	use Edde\Api\Crate\ICrate;
	use Edde\Api\Crate\IValue;
	use Edde\Api\Schema\ISchema;
	use Edde\Common\AbstractObject;

	class Crate extends AbstractObject implements ICrate {
		/**
		 * @var ISchema
		 */
		protected $schema;
		/**
		 * @var IValue[]
		 */
		protected $valueList = [];

		/**
		 * @param ISchema $schema
		 */
		public function __construct(ISchema $schema) {
			$this->schema = $schema;
		}

		public function getSchema() {
			return $this->schema;
		}

		public function getValueList() {
			return $this->valueList;
		}
	}
