<?php
	declare(strict_types = 1);

	namespace Edde\Common\Node;

	use Edde\Api\Node\IAbstractNode;
	use Edde\Api\Node\INode;

	/**
	 * Default full featured node implementation.
	 */
	class Node extends AbstractNode implements INode {
		/**
		 * @var string
		 */
		protected $name;
		protected $attributeList = [];
		protected $attributeNamespaceList = [];
		/**
		 * @var mixed
		 */
		protected $value;
		/**
		 * abstract metadata
		 *
		 * @var array
		 */
		protected $metaList = [];

		/**
		 * @param string $name
		 * @param array $attributeList
		 * @param mixed|null $value
		 */
		public function __construct($name = null, $value = null, array $attributeList = []) {
			parent::__construct();
			$this->name = $name;
			$this->value = $value;
			$this->attributeList = $attributeList;
		}

		/**
		 * @param string $name
		 * @param array $attributeList
		 * @param mixed|null $value
		 *
		 * @return INode|static
		 */
		static public function create($name = null, $value = null, array $attributeList = []) {
			return new static($name, $value, $attributeList);
		}

		public function getPath($attribute = false, $meta = false) {
			$current = $this;
			$path = [];
			while ($current) {
				$fragment = $current->getName();
				if ($attribute && empty($current->attributeList) === false) {
					$fragment .= '[' . implode('][', array_keys($current->attributeList)) . ']';
				}
				if ($meta && empty($current->metaList) === false) {
					$fragment .= '(' . implode(')(', array_keys($current->metaList)) . ')';
				}
				$path[] = $fragment;
				$current = $current->getParent();
			}
			return '/' . implode('/', array_reverse($path));
		}

		public function getName() {
			return $this->name;
		}

		public function setName($name) {
			$this->name = $name;
			return $this;
		}

		public function getValue($default = null) {
			return $this->value !== null ? $this->value : $default;
		}

		public function setValue($value) {
			$this->value = $value;
			return $this;
		}

		public function hasAttribute($name) {
			return isset($this->attributeList[$name]) || array_key_exists($name, $this->attributeList);
		}

		public function getAttribute($name, $default = null) {
			return $this->attributeList[$name] ?? $default;
		}

		public function hasAttributeList(string $namespace): bool {
			$this->getAttributeList($namespace);
			return empty($this->attributeNamespaceList[$namespace]) === false;
		}

		public function getAttributeList(string $namespace = null): array {
			if (isset($this->attributeNamespaceList[$namespace]) === false) {
				$key = $namespace ? "$namespace:" : '';
				$this->attributeNamespaceList[$namespace] = [];
				foreach ($this->attributeList as $name => $value) {
					if ($key !== '' && strpos($name, $key) === false) {
						continue;
					}
					$this->attributeNamespaceList[$namespace][str_replace($key, '', $name)] = $value;
				}
			}
			return $this->attributeNamespaceList[$namespace];
		}

		public function setAttributeList(array $attributeList) {
			$this->attributeNamespaceList = [];
			$this->attributeList = $attributeList;
			return $this;
		}

		public function removeAttributeList(string $namespace): INode {
			unset($this->attributeNamespaceList[$namespace]);
			foreach ($this->attributeList as $name => $value) {
				$key = "$namespace:";
				if (strpos($name, $key) === false) {
					continue;
				}
				unset($this->attributeList[$name]);
			}
			return $this;
		}

		public function addAttributeList(array $attributeList) {
			$this->attributeNamespaceList = [];
			foreach ($attributeList as $name => $value) {
				$this->setAttribute($name, $value);
			}
			return $this;
		}

		public function setAttribute($name, $value) {
			$this->attributeNamespaceList = [];
			$this->attributeList[$name] = $value;
			return $this;
		}

		public function hasMeta($name) {
			return isset($this->metaList[$name]) || array_key_exists($name, $this->metaList);
		}

		public function getMeta($name, $default = null) {
			return $this->metaList[$name] ?? $default;
		}

		public function getMetaList() {
			return $this->metaList;
		}

		public function setMetaList(array $metaList) {
			$this->metaList = $metaList;
			return $this;
		}

		public function addMetaList(array $metaList) {
			foreach ($metaList as $name => $value) {
				$this->setMeta($name, $value);
			}
			return $this;
		}

		public function setMeta($name, $value) {
			$this->metaList[$name] = $value;
			return $this;
		}

		public function accept(IAbstractNode $abstractNode) {
			return $abstractNode instanceof INode;
		}
	}
