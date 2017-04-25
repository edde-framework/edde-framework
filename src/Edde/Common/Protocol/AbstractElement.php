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
			if (empty($this->data) === false) {
				$packet->data = $this->data;
			}
			return $packet;
		}
	}
