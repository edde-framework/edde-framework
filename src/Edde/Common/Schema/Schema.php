<?php
	namespace Edde\Common\Schema;

	use Edde\Api\Schema\ISchema;
	use Edde\Common\AbstractObject;

	class Schema extends AbstractObject implements ISchema {
		/**
		 * @var string
		 */
		private $name;
		/**
		 * @var string
		 */
		private $namespace;
		/**
		 * @var string
		 */
		private $schemaName;

		/**
		 * @param string $name
		 * @param string $namespace
		 */
		public function __construct($name, $namespace = null) {
			$this->name = $name;
			$this->namespace = $namespace;
		}

		public function getName() {
			return $this->name;
		}

		public function getNamespace() {
			return $this->namespace;
		}

		public function getSchemaName() {
			if ($this->schemaName === null) {
				$this->schemaName = (($namespace = $this->namespace) !== null ? $namespace . '\\' : null) . $this->name;
			}
			return $this->schemaName;
		}
	}
