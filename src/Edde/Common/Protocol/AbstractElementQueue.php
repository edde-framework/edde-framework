<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\IElementQueue;
	use Edde\Api\Protocol\LazyProtocolServiceTrait;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractElementQueue extends Object implements IElementQueue {
		use LazyProtocolServiceTrait;
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
		public function execute(): IElementQueue {
			foreach ($this->queueList as $element) {
				/** @var $response IElement */
				if (($response = $this->protocolService->execute($element)) instanceof IElement) {
					$this->elementList[$response->getId()] = $response;
				}
				/**
				 * null because later empty items will be filtered out
				 */
				$this->queueList[$element->getId()] = null;
			}
			return $this;
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
		public function isEmpty(): bool {
			return empty($this->queueList);
		}

		/**
		 * @inheritdoc
		 */
		public function clear(): IElementQueue {
			$this->queueList = [];
			$this->elementList = [];
			return $this;
		}
	}
