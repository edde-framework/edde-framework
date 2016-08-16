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
		 * @var bool
		 */
		protected $array;

		/**
		 * @param ISchema $schema
		 * @param string $name
		 * @param string $type
		 * @param bool $required
		 * @param bool $unique
		 * @param bool $identifier
		 * @param bool $array
		 */
		public function __construct(ISchema $schema, $name, $type = 'string', $required = false, $unique = false, $identifier = false, $array = false) {
			$this->schema = $schema;
			$this->name = $name;
			$this->type = $type;
			$this->required = $required;
			$this->unique = $unique;
			$this->identifier = $identifier;
			$this->array = $array;
		}

		public function getSchema(): ISchema {
			return $this->schema;
		}

		public function getName(): string {
			return $this->name;
		}

		public function getPropertyName(): string {
			if ($this->propertyName === null) {
				$this->propertyName = $this->schema->getSchemaName() . '::' . $this->name;
			}
			return $this->propertyName;
		}

		public function type(string $type) {
			$this->type = $type;
			return $this;
		}

		public function required(bool $required = true) {
			$this->required = $required;
			return $this;
		}

		public function unique(bool $unique = true) {
			$this->unique = $unique;
			return $this;
		}

		public function identifier(bool $identifier = true) {
			$this->identifier = $identifier;
			return $this;
		}

		public function array(bool $array = true) {
			$this->array = $array;
			return $this;
		}

		public function isIdentifier(): bool {
			return $this->identifier;
		}

		public function getType(): string {
			return $this->type;
		}

		public function isRequired(): bool {
			return $this->required;
		}

		public function isUnique(): bool {
			return $this->unique;
		}

		public function isArray(): bool {
			return $this->array;
		}
	}
