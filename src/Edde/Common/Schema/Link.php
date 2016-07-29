<?php
	namespace Edde\Common\Schema;

	use Edde\Api\Schema\ILink;
	use Edde\Api\Schema\IProperty;
	use Edde\Common\AbstractObject;

	class Link extends AbstractObject implements ILink {
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var IProperty
		 */
		protected $source;
		/**
		 * @var IProperty
		 */
		protected $target;

		/**
		 * @param string $name
		 * @param IProperty $source
		 * @param IProperty $target
		 */
		public function __construct($name, IProperty $source, IProperty $target) {
			$this->name = $name;
			$this->source = $source;
			$this->target = $target;
		}

		public function getName() {
			return $this->name;
		}

		public function getSource() {
			return $this->source;
		}

		public function getTarget() {
			return $this->target;
		}
	}
