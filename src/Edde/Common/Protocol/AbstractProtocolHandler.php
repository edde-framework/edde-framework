<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Http\LazyHostUrlTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\IProtocolHandler;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractProtocolHandler extends Object implements IProtocolHandler {
		use LazyContainerTrait;
		use LazyHostUrlTrait;
		use ConfigurableTrait;
		/**
		 * @var IElement[]
		 */
		protected $queueList = [];
		/**
		 * dequeued elements by ID
		 *
		 * @var IElement[]
		 */
		protected $elementList = [];

		/**
		 * @inheritdoc
		 */
		public function check(IElement $element): IProtocolHandler {
			if ($this->canHandle($element)) {
				return $this;
			}
			throw new UnsupportedElementException(sprintf('Unsupported element [%s] in protocol handler [%s].', $element->getName(), static::class));
		}

		/**
		 * @inheritdoc
		 */
		public function queue(IElement $element): IProtocolHandler {
			$this->check($element);
			$this->queueList[] = $element;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function element(IElement $element) {
			$this->check($element);
			if ($element->isAsync()) {
				$this->queue($element);
				return $this->createAsyncElement($element);
			}
			return $this->execute($element);
		}

		/**
		 * @inheritdoc
		 */
		public function dequeue(string $scope = null, array $tagList = null): IProtocolHandler {
			foreach ($this->getQueueList($scope, $tagList) as $element) {
				/** @var $response INode */
				if (($response = $this->execute($element)) instanceof IElement) {
					$this->elementList[] = $response;
				}
			}
			return $this;
		}

		/**
		 * @param string|null $scope
		 * @param array|null  $tagList
		 *
		 * @return \Generator|IElement[]
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
		public function packet(string $scope = null, array $tagList = null, IElement $element = null): IElement {
			return ($element ?: (new Packet($this->hostUrl->getAbsoluteUrl()))->setScope($scope)->setTagList($tagList))->setElementList('elements', iterator_to_array($this->getQueueList($scope, $tagList)));
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
		 * @param IElement $element
		 *
		 * @return IElement|null
		 */
		protected function createAsyncElement(IElement $element) {
			return null;
		}
	}
