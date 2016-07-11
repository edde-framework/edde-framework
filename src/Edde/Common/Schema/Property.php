<?php
	namespace Edde\Common\Schema;

	use Edde\Api\Schema\ILink;
	use Edde\Api\Schema\IProperty;
	use Edde\Api\Schema\ISchema;
	use Edde\Api\Schema\PropertyException;
	use Edde\Common\AbstractObject;

	class Property extends AbstractObject implements IProperty {
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
		 * @var ILink
		 */
		protected $link;

		/**
		 * @param ISchema $schema
		 * @param string $name
		 * @param string $type
		 * @param bool $required
		 * @param bool $unique
		 * @param bool $identifier
		 * @param ILink $link
		 */
		public function __construct(ISchema $schema, $name, $type = null, $required = true, $unique = false, $identifier = false, ILink $link = null) {
			$this->schema = $schema;
			$this->name = $name;
			$this->type = $type;
			$this->required = $required;
			$this->unique = $unique;
			$this->identifier = $identifier;
			$this->link = $link;
		}

		public function getSchema() {
			return $this->schema;
		}

		public function getName() {
			return $this->name;
		}

		public function getPropertyName() {
			return $this->schema->getSchemaName() . '::' . $this->name;
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

		public function getLink() {
			if ($this->isLink() === false) {
				throw new PropertyException(sprintf('Property definition [%s] is not a link.', $this));
			}
			return $this->link;
		}

		public function isLink() {
			return $this->link !== null;
		}
	}
