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
		 * @var bool
		 */
		protected $singleLink;

		/**
		 * @param string $name
		 * @param IProperty $source
		 * @param IProperty $target
		 * @param bool $singleLink
		 */
		public function __construct($name, IProperty $source, IProperty $target, $singleLink = false) {
			$this->name = $name;
			$this->source = $source;
			$this->target = $target;
			$this->singleLink = $singleLink;
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

		public function isMultiLink() {
			return $this->isSingleLink() === false;
		}

		public function isSingleLink() {
			return $this->singleLink === true;
		}
	}
