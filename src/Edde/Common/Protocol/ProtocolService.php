<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Http\LazyHostUrlTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\IProtocolHandler;
	use Edde\Api\Protocol\IProtocolService;

	class ProtocolService extends AbstractProtocolHandler implements IProtocolService {
		use LazyContainerTrait;
		use LazyHostUrlTrait;
		/**
		 * @var IProtocolHandler[]
		 */
		protected $protocolHandlerList = [];
		/**
		 * @var IProtocolHandler[]
		 */
		protected $handleList = [];

		/**
		 * @inheritdoc
		 */
		public function registerProtocolHandler(IProtocolHandler $protocolHandler): IProtocolService {
			$this->protocolHandlerList[] = $protocolHandler;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getProtocolHandler(IElement $element): IProtocolHandler {
			if (isset($this->handleList[$type = $element->getType()])) {
				return $this->handleList[$type];
			}
			foreach ($this->getProtocolHandleList() as $protocolHandler) {
				if ($protocolHandler->canHandle($element)) {
					return $this->handleList[$type] = $protocolHandler;
				}
			}
			throw new UnsupportedElementException(sprintf('There is no protocol handler for the given element [%s].', $type));
		}

		/**
		 * @inheritdoc
		 */
		public function getProtocolHandleList() {
			foreach ($this->protocolHandlerList as $protocolHandler) {
				$protocolHandler->setup();
				yield $protocolHandler;
			}
		}

		/**
		 * @inheritdoc
		 */
		public function dequeue(): IProtocolService {
			$this->elementQueue->execute();
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getQueueList(string $scope, array $tagList = null) {
			return $this->elementQueue->getQueueList($scope, $tagList);
		}

		/**
		 * @inheritdoc
		 */
		public function createQueuePacket(string $scope, array $tagList = null): IElement {
			return (new Packet($this->hostUrl->getAbsoluteUrl()))->setScope($scope)->setTagList($tagList)->setElementList('elements', iterator_to_array($this->getQueueList($scope, $tagList)));
		}

		/**
		 * @inheritdoc
		 */
		public function getReferenceList(string $id): array {
			return $this->elementQueue->getReferenceList($id);
		}

		/**
		 * @inheritdoc
		 */
		public function canHandle(IElement $element): bool {
			return $this->getProtocolHandler($element)->canHandle($element);
		}

		/**
		 * @inheritdoc
		 */
		public function element(IElement $element) {
			return $this->getProtocolHandler($element)->element($element);
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IElement $element) {
			return $this->getProtocolHandler($element)->execute($element);
		}
	}
