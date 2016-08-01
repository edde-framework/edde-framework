<?php
	namespace Edde\Common\Schema;

	use Edde\Api\Schema\ILink;
	use Edde\Api\Schema\IProperty;
	use Edde\Api\Schema\ISchema;
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
		 * @var ILink[]
		 */
		protected $linkList = [];

		/**
		 * @param ISchema $schema
		 * @param string $name
		 * @param string $type
		 * @param bool $required
		 * @param bool $unique
		 * @param bool $identifier
		 */
		public function __construct(ISchema $schema, $name, $type = null, $required = true, $unique = false, $identifier = false) {
			$this->schema = $schema;
			$this->name = $name;
			$this->type = $type;
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

		public function link(IProperty $property, $name = null, $multiLink = true) {
			/**
			 * first try if a schema will accept a new link with the given name
			 */
			$this->schema->addLink($link = new Link($name ?: $this->name, $this, $property, $multiLink === false));
			$this->linkList[] = $link;
			return $this;
		}

		public function getLinkList() {
			return $this->linkList;
		}

		public function isLink() {
			return empty($this->linkList) === false;
		}
	}
