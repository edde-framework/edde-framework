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
		protected $id;
		/**
		 * @var IElement
		 */
		protected $reference;
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
		 * @param string      $type
		 * @param string|null $id
		 */
		public function __construct(string $type, string $id = null) {
			$this->type = $type;
			$this->id = $id;
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
		public function setId(string $id): IElement {
			$this->id = $id;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getId(): string {
			return $this->id === null ? ($this->id = bin2hex(random_bytes(4)) . '-' . implode('-', str_split(bin2hex(random_bytes(8)), 4)) . '-' . bin2hex(random_bytes(6))) : $this->id;
		}

		/**
		 * @inheritdoc
		 */
		public function setReference(IElement $element): IElement {
			$this->reference = $element;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function hasReference(): bool {
			return $this->reference !== null;
		}

		/**
		 * @inheritdoc
		 */
		public function isReferenceOf(IElement $element): bool {
			return $this->reference !== null ? $this->reference->getId() === $element->getId() : false;
		}

		/**
		 * @inheritdoc
		 */
		public function getReference(): IElement {
			if ($this->hasReference() === false) {
				throw new ReferenceException(sprintf('Element [%s (%s)] has no reference set.', static::class, $this->getType()));
			}
			return $this->reference;
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
		public function inScope(string $scope = null): bool {
			return $this->scope === $scope;
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
		public function inTagList(array $tagList = null, bool $strict = false): bool {
			if ($tagList === null && $this->tagList === null) {
				return true;
			} else if ($strict === false && empty($tagList) && empty($this->tagList)) {
				return true;
			} else if ($tagList === null && $this->tagList !== null) {
				return false;
			}
			$diff = array_diff($tagList, $this->tagList);
			if ($strict) {
				return empty($diff) && count($this->tagList) === count($tagList);
			}
			return count($diff) !== count($tagList);
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
		public function put(array $data): IElement {
			$this->data = $data;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function get(string $name, $default = null) {
			return $this->data[$name] ?? $default;
		}

		/**
		 * @inheritdoc
		 */
		public function array(): array {
			return $this->data;
		}

		/**
		 * @return \stdClass
		 */
		public function packet(): \stdClass {
			$packet = new \stdClass();
			$packet->type = $this->type;
			$packet->id = $this->getId();
			if (empty($this->scope) === false) {
				$packet->scope = $this->scope;
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
