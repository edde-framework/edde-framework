<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Protocol\IElement;
	use Edde\Common\Object;

	abstract class AbstractElement extends Object implements IElement {
		/**
		 * @var string
		 */
		protected $type;
		/**
		 * @var string
		 */
		protected $scope;
		/**
		 * @var string[]
		 */
		protected $tagList = [];
		/**
		 * @var array
		 */
		protected $data;

		/**
		 * @param string $type
		 */
		public function __construct(string $type) {
			$this->type = $type;
		}

		/**
		 * @inheritdoc
		 */
		public function getType(): string {
			return $this->type;
		}

		/**
		 * @inheritdoc
		 */
		public function setScope(string $scope = null): IElement {
			$this->scope = $scope;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getScope() {
			return $this->scope;
		}

		/**
		 * @inheritdoc
		 */
		public function setTagList(array $tagList = null): IElement {
			$this->tagList = $tagList ?: [];
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getTagList(): array {
			return $this->tagList;
		}

		/**
		 * @inheritdoc
		 */
		public function set(string $name, $value): IElement {
			$this->data[$name] = $value;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function get(string $name, $default = null) {
			return $this->data[$name] ?? $default;
		}

		/**
		 * @return \stdClass
		 */
		public function packet(): \stdClass {
			$packet = new \stdClass();
			$packet->type = $this->type;
			if (empty($this->scope) === false) {
				$packet->sope = $this->scope;
			}
			if (empty($this->tagList) === false) {
				$packet->tags = $this->tagList;
			}
			if (empty($this->data) === false) {
				$packet->data = $this->data;
			}
			return $packet;
		}
	}
