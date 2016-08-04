<?php
	declare(strict_types = 1);

	namespace Edde\Common\Schema;

	use Edde\Api\Schema\ISchema;
	use Edde\Api\Schema\ISchemaProperty;
	use Edde\Common\AbstractObject;

	class SchemaProperty extends AbstractObject implements ISchemaProperty {
		/**
		 * @var ISchema
		 */
		protected $schema;
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var string
		 */
		protected $propertyName;
		/**
		 * @var string
		 */
		protected $type;
		/**
		 * @var bool
		 */
		protected $required;
		/**
		 * @var bool
		 */
		protected $unique;
		/**
		 * @var bool
		 */
		protected $identifier;

		/**
		 * @param ISchema $schema
		 * @param string $name
		 * @param bool $required
		 * @param bool $unique
		 * @param bool $identifier
		 */
		public function __construct(ISchema $schema, $name, $required = false, $unique = false, $identifier = false) {
			$this->schema = $schema;
			$this->name = $name;
			$this->type = 'string';
			$this->required = $required;
			$this->unique = $unique;
			$this->identifier = $identifier;
		}

		public function getSchema() {
			return $this->schema;
		}

		public function getName() {
			return $this->name;
		}

		public function getPropertyName() {
			if ($this->propertyName === null) {
				$this->propertyName = $this->schema->getSchemaName() . '::' . $this->name;
			}
			return $this->propertyName;
		}

		public function type($type) {
			$this->type = $type;
			return $this;
		}

		public function required($required = true) {
			$this->required = $required;
			return $this;
		}

		public function unique($unique = true) {
			$this->unique = $unique;
			return $this;
		}

		public function identifier($identifier = true) {
			$this->identifier = $identifier;
			return $this;
		}

		public function isIdentifier() {
			return $this->identifier;
		}

		public function getType() {
			return $this->type;
		}

		public function isRequired() {
			return $this->required;
		}

		public function isUnique() {
			return $this->unique;
		}
	}
