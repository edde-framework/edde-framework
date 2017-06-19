<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\IElementQueue;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractElementQueue extends Object implements IElementQueue {
		use ConfigurableTrait;
		/**
		 * @var IElement[]
		 */
		protected $elementList = [];
		/**
		 * @var IElement[]
		 */
		protected $referenceList = [];

		/**
		 * @inheritdoc
		 */
		public function queue(IElement $element): IElementQueue {
			$this->elementList[$element->getId()] = $element;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getElementList() {
			return $this->elementList;
		}

		/**
		 * @inheritdoc
		 */
		public function getReferenceList() {
			return $this->referenceList;
		}

		/**
		 * @inheritdoc
		 */
		public function getReferenceListBy(string $id): array {
			$elementList = [];
			foreach ($this->referenceList as $element) {
				$elementList = array_merge($elementList, $element->getReferenceList($id));
			}
			return $elementList;
		}

		/**
		 * @inheritdoc
		 */
		public function addReference(IElement $element): IElementQueue {
			$this->referenceList[$element->getId()] = $element;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function isEmpty(): bool {
			return empty($this->elementList);
		}

		/**
		 * @inheritdoc
		 */
		public function clear(): IElementQueue {
			$this->clearQueue();
			$this->referenceList = [];
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function clearQueue(): IElementQueue {
			$this->elementList = [];
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getIterator() {
			return new \ArrayIterator($this->elementList);
		}
	}
