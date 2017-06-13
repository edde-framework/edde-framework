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
		protected $queueList = [];
		/**
		 * @var IElement[]
		 */
		protected $elementList = [];

		/**
		 * @inheritdoc
		 */
		public function queue(IElement $element): IElementQueue {
			$this->queueList[$element->getId()] = $element;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getQueueList(string $scope = null, array $tagList = null) {
			foreach ($this->queueList as $element) {
				if ($element->inScope($scope) && ($tagList ? $element->hasTagList($tagList) : true)) {
					yield $element;
				}
			}
		}

		/**
		 * @inheritdoc
		 */
		public function getReferenceList(string $id): array {
			$elementList = [];
			foreach ($this->elementList as $element) {
				$elementList = array_merge($elementList, $element->getReferenceList($id));
			}
			return $elementList;
		}

		/**
		 * @inheritdoc
		 */
		public function addReference(IElement $element): IElementQueue {
			$this->elementList[$element->getId()] = $element;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function isEmpty(): bool {
			return empty($this->queueList);
		}

		/**
		 * @inheritdoc
		 */
		public function clear(): IElementQueue {
			$this->clearQueue();
			$this->elementList = [];
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function clearQueue(): IElementQueue {
			$this->queueList = [];
			return $this;
		}
	}
