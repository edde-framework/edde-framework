<?php
	declare(strict_types = 1);

	namespace Edde\Common\Crate;

	use Edde\Api\Crate\CrateException;
	use Edde\Api\Crate\ICollection;
	use Edde\Api\Crate\ICrate;
	use Edde\Api\Crate\IProperty;
	use Edde\Api\Schema\ISchema;
	use Edde\Common\Schema\Schema;
	use Edde\Common\Usable\AbstractUsable;

	class Crate extends AbstractUsable implements ICrate {
		/**
		 * @var ISchema
		 */
		protected $schema;
		/**
		 * @var IProperty[]
		 */
		protected $propertyList = [];
		/**
		 * @var IProperty[]
		 */
		protected $identifierList;
		/**
		 * @var ICollection[]
		 */
		protected $collectionList = [];
		/**
		 * @var ICrate[]
		 */
		protected $linkList = [];
		/**
		 * @var string[]
		 */
		protected $propertyNameList = [];

		public function getSchema(): ISchema {
			if ($this->schema === null) {
				throw new CrateException(sprintf('Cannot get schema from anonymous crate [%s].', static::class));
			}
			return $this->schema;
		}

		public function setSchema(ISchema $schema): ICrate {
			if ($this->isUsed()) {
				throw new CrateException(sprintf('Cannot set schema [%s] to already prepared crate [%s].', $schema->getSchemaName(), static::class));
			}
			$this->schema = $schema;
			return $this;
		}

		public function getPropertyList(): array {
			$this->use();
			return $this->propertyList;
		}

		public function getIdentifierList(): array {
			$this->use();
			if ($this->identifierList === null) {
				$this->identifierList = [];
				foreach ($this->propertyList as $property) {
					$schemaProperty = $property->getSchemaProperty();
					if ($schemaProperty->isIdentifier()) {
						$this->identifierList[] = $property;
					}
				}
			}
			return $this->identifierList;
		}

		public function put(array $put, bool $strict = true): ICrate {
			$this->use();
			if ($strict && ($diff = array_diff(array_keys($put), $this->propertyNameList)) !== []) {
				throw new CrateException(sprintf('Setting unknown values [%s] to the crate [%s].', implode(', ', $diff), $this->schema->getSchemaName()));
			}
			foreach ($put as $property => $value) {
				if (isset($this->propertyList[$property]) === false) {
					continue;
				}
				$this->set($property, $value);
			}
			return $this;
		}

		public function set(string $name, $value): ICrate {
			$this->use();
			$this->getProperty($name)
				->set($value);
			return $this;
		}

		public function getProperty(string $name): IProperty {
			$this->use();
			if ($this->hasProperty($name) === false) {
				throw new CrateException(sprintf('Unknown value [%s] in crate [%s].', $name, $this->schema->getSchemaName()));
			}
			return $this->propertyList[$name];
		}

		public function hasProperty(string $name): bool {
			$this->use();
			return isset($this->propertyList[$name]);
		}

		public function add(string $name, $value, $key = null): ICrate {
			$property = $this->getProperty($name)
				->getSchemaProperty();
			if ($property->isArray() === false) {
				throw new CrateException(sprintf('Property [%s] is not array; cannot add value.', $property->getPropertyName()));
			}
			$array = $this->get($name);
			if ($key === null) {
				$array[] = $value;
			} else {
				$array[$key] = $value;
			}
			$this->set($name, $array);
			return $this;
		}

		public function get(string $name, $default = null) {
			$this->use();
			return $this->getProperty($name)
				->get($default);
		}

		public function push(array $push, bool $strict = true): ICrate {
			$this->use();
			if ($strict && ($diff = array_diff(array_keys($push), $this->propertyNameList)) !== []) {
				throw new CrateException(sprintf('Setting unknown values [%s] to the crate [%s].', implode(', ', $diff), $this->schema->getSchemaName()));
			}
			foreach ($push as $property => $value) {
				$property = $this->getProperty($property);
				$schemaProperty = $property->getSchemaProperty();
				if (($isArray = is_array($value)) === false && $schemaProperty->isArray()) {
					throw new CrateException(sprintf('Cannot push simple value [%s] to array.', $property->getSchemaProperty()));
				}
				if ($isArray && $schemaProperty->isArray() === false) {
					throw new CrateException(sprintf('Cannot push array to simple value [%s].', $property->getSchemaProperty()));
				}
				$property->push($value);
			}
			return $this;
		}

		public function linkTo(array $linkTo): ICrate {
			foreach ($linkTo as $name => $crate) {
				$this->link($name, $crate);
			}
			return $this;
		}

		public function link(string $name, ICrate $crate): ICrate {
			if ($this->schema->hasLink($name) === false) {
				throw new CrateException(sprintf('Crate [%s] has no link [%s] in schema [%s].', static::class, $name, $this->schema->getSchemaName()));
			}
			$link = $this->schema->getLink($name);
			$this->linkList[$name] = $crate;
			$this->set($link->getSource()
				->getName(), $crate->get($link->getTarget()
				->getName()));
			return $this;
		}

		public function getLink(string $name) {
			if ($this->hasLink($name) === false) {
				throw new CrateException(sprintf('Requested unknown link [%s] on the crate [%s].', $name, $this->schema->getSchemaName()));
			}
			return $this->linkList[$name];
		}

		public function hasLink(string $name): bool {
			return isset($this->linkList[$name]);
		}

		public function collection(string $name, ICollection $collection): ICrate {
			if ($this->schema->hasCollection($name) === false) {
				throw new CrateException(sprintf('Crate [%s] has no collection [%s] in schema [%s].', static::class, $name, $this->schema->getSchemaName()));
			}
			$this->collectionList[$name] = $collection;
			return $this;
		}

		public function getCollection(string $name): ICollection {
			if ($this->hasCollection($name) === false) {
				throw new CrateException(sprintf('Requested unknown collection [%s] on the crate [%s].', $name, $this->schema->getSchemaName()));
			}
			return $this->collectionList[$name];
		}

		public function hasCollection(string $name): bool {
			return isset($this->collectionList[$name]);
		}

		public function getDirtyList(): array {
			$this->use();
			if ($this->isDirty() === false) {
				return [];
			}
			$propertyList = [];
			foreach ($this->propertyList as $property) {
				if ($property->isDirty() === false) {
					continue;
				}
				$schemaProperty = $property->getSchemaProperty();
				$propertyList[$schemaProperty->getName()] = $property;
			}
			return $propertyList;
		}

		public function isDirty(): bool {
			$this->use();
			foreach ($this->propertyList as $property) {
				if ($property->isDirty()) {
					return true;
				}
			}
			return false;
		}

		public function __clone() {
			if ($this->isUsed()) {
				throw new CrateException(sprintf('Cannot clone used crate [%s].', $this->schema->getSchemaName()));
			}
		}

		protected function prepare() {
			if ($this->schema === null) {
				$this->schema = new Schema('anonymous-crate');
			}
			foreach ($this->schema->getPropertyList() as $property) {
				$this->addProperty(new Property($property));
			}
			$this->propertyNameList = array_merge(array_keys($this->propertyList), array_keys($this->schema->getLinkList()), array_keys($this->schema->getCollectionList()));
		}

		public function addProperty(IProperty $property, bool $force = false): ICrate {
			$this->use();
			$schemaProperty = $property->getSchemaProperty();
			if (isset($this->propertyList[$propertyName = $schemaProperty->getName()]) && $force === false) {
				throw new CrateException(sprintf('Property [%s] is already present in crate [%s].', $propertyName, $this->schema->getSchemaName()));
			}
			$this->propertyList[$propertyName] = $property;
			return $this;
		}
	}
