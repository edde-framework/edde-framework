<?php
	declare(strict_types = 1);

	namespace Edde\Common\Crate;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crate\CrateException;
	use Edde\Api\Crate\ICollection;
	use Edde\Api\Crate\ICrate;
	use Edde\Api\Crate\IProperty;
	use Edde\Api\Schema\ISchema;
	use Edde\Common\Schema\Schema;
	use Edde\Common\Usable\AbstractUsable;

	class Crate extends AbstractUsable implements ICrate {
		/**
		 * @var IContainer
		 */
		protected $container;
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

		/**
		 * @param IContainer $container
		 */
		public function __construct(IContainer $container) {
			$this->container = $container;
		}

		public function getSchema() {
			if ($this->schema === null) {
				throw new CrateException(sprintf('Cannot get schema from anonymous crate [%s].', static::class));
			}
			return $this->schema;
		}

		public function setSchema(ISchema $schema) {
			if ($this->isUsed()) {
				throw new CrateException(sprintf('Cannot set schema [%s] to already prepared crate [%s].', $schema->getSchemaName(), static::class));
			}
			$this->schema = $schema;
			return $this;
		}

		public function getPropertyList() {
			$this->use();
			return $this->propertyList;
		}

		public function getIdentifierList() {
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

		public function put(array $put, $strict = true) {
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

		public function set($name, $value) {
			$this->use();
			$this->getProperty($name)
				->set($value);
			return $this;
		}

		public function getProperty($name) {
			$this->use();
			if ($this->hasProperty($name) === false) {
				throw new CrateException(sprintf('Unknown value [%s] in crate [%s].', $name, $this->schema->getSchemaName()));
			}
			return $this->propertyList[$name];
		}

		public function hasProperty($name) {
			$this->use();
			return isset($this->propertyList[$name]);
		}

		public function add($name, $value, $key = null) {
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

		public function get($name, $default = null) {
			$this->use();
			return $this->getProperty($name)
				->get($default);
		}

		public function push(array $push, $strict = true) {
			$this->use();
			if ($strict && ($diff = array_diff(array_keys($push), $this->propertyNameList)) !== []) {
				throw new CrateException(sprintf('Setting unknown values [%s] to the crate [%s].', implode(', ', $diff), $this->schema->getSchemaName()));
			}
			foreach ($push as $property => $value) {
				if ($this->schema->hasCollection($property)) {
					$collection = $this->collection($property);
					/** @var $value array */
					foreach ($value as $collectionValue) {
						if (is_array($collectionValue) === false) {
							throw new CrateException(sprintf('Cannot push source value into the crate [%s]; value [%s] is not an array (collection).', $this->schema->getSchemaName(), $property));
						}
						$crate = $collection->createCrate();
						$crate->push($collectionValue);
						$collection->addCrate($crate);
					}
					continue;
				} else if ($this->schema->hasLink($property)) {
					$link = $this->link($property);
					$link->push($value);
					continue;
				}
				if (isset($this->propertyList[$property]) === false) {
					continue;
				}
				$property = $this->getProperty($property);
				$schemaProperty = $property->getSchemaProperty();
				if ($schemaProperty->isArray() && is_array($value) === false) {
					throw new CrateException(sprintf('Cannot push simple value [%s] to array.', $property->getSchemaProperty()));
				}
				if ($schemaProperty->isArray() === false && is_array($value)) {
					throw new CrateException(sprintf('Cannot push array to simple value [%s].', $property->getSchemaProperty()));
				}
				$property->push($value);
			}
			return $this;
		}

		public function collection($name) {
			if ($this->schema->hasCollection($name) === false) {
				throw new CrateException(sprintf('Crate [%s] has no collection [%s] in schema [%s].', static::class, $name, $this->schema->getSchemaName()));
			}
			if (isset($this->collectionList[$name]) === false) {
				$collection = $this->schema->getCollection($name);
				$this->collectionList[$name] = $this->container->create(Collection::class, $collection->getTarget()
					->getSchema());
			}
			return $this->collectionList[$name];
		}

		public function link($name) {
			if ($this->schema->hasLink($name) === false) {
				throw new CrateException(sprintf('Crate [%s] has no link [%s] in schema [%s].', static::class, $name, $this->schema->getSchemaName()));
			}
			if (isset($this->linkList[$name]) === false) {
				$link = $this->schema->getLink($name);
				$targetSchema = $link->getTarget()
					->getSchema();
				/** @var $crate ICrate */
				$this->linkList[$name] = $crate = $this->container->create($targetSchema->getSchemaName());
				$crate->setSchema($targetSchema);
			}
			return $this->linkList[$name];
		}

		public function setCollection($name, ICollection $collection) {
			if ($this->schema->hasCollection($name) === false) {
				throw new CrateException(sprintf('Crate [%s] has no collection [%s] in schema [%s].', static::class, $name, $this->schema->getSchemaName()));
			}
			$this->collectionList[$name] = $collection;
			return $this;
		}

		public function setLink($name, ICrate $crate) {
			if ($this->schema->hasLink($name) === false) {
				throw new CrateException(sprintf('Crate [%s] has no link [%s] in schema [%s].', static::class, $name, $this->schema->getSchemaName()));
			}
			$this->linkList[$name] = $crate;
			return $this;
		}

		public function getDirtyList() {
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

		public function isDirty() {
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

		public function addProperty(IProperty $property, $force = false) {
			$this->use();
			$schemaProperty = $property->getSchemaProperty();
			if (isset($this->propertyList[$propertyName = $schemaProperty->getName()]) && $force === false) {
				throw new CrateException(sprintf('Property [%s] is already present in crate [%s].', $propertyName, $this->schema->getSchemaName()));
			}
			$this->propertyList[$propertyName] = $property;
			return $this;
		}
	}
