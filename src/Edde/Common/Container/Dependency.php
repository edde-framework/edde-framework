<?php
	namespace Edde\Common\Container;

	use Edde\Api\Container\IDependency;
	use Edde\Api\Node\IAbstractNode;
	use Edde\Common\Node\Node;

	class Dependency extends Node implements IDependency {
		/**
		 * @param string $name
		 * @param bool $mandatory mandatory means needed in constructor
		 * @param bool $optional optional can be optional, including constructor
		 */
		public function __construct($name, $mandatory, $optional) {
			parent::__construct($name, null, [
				'mandatory' => $mandatory,
				'optional' => $optional,
			]);
		}

		/**
		 * return dependency list of this dependency
		 *
		 * @return IDependency[]
		 */
		public function getDependencyList() {
			return $this->getNodeList();
		}

		public function accept(IAbstractNode $abstractNode) {
			return $abstractNode instanceof IDependency;
		}
	}
