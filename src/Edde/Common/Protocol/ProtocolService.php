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
		public function canHandle(IElement $element): bool {
			return $this->getProtocolHandler($element)->canHandle($element);
		}

		/**
		 * @inheritdoc
		 */
		public function queue(IElement $element): IProtocolHandler {
			$this->getProtocolHandler($element)->queue($element);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function dequeue(string $scope = null, array $tagList = null): IProtocolHandler {
			foreach ($this->getProtocolHandleList() as $protocolHandler) {
				$protocolHandler->dequeue($scope, $tagList);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function iterate(string $scope = null, array $tagList = null) {
			foreach ($this->getProtocolHandleList() as $protocolHandler) {
				foreach ($protocolHandler->iterate($scope, $tagList) as $element) {
					yield $element;
				}
			}
		}

		/**
		 * @inheritdoc
		 */
		public function getReferenceList(string $id): array {
			$elementList = [];
			foreach ($this->getProtocolHandleList() as $protocolHandler) {
				$elementList = array_merge($elementList, $protocolHandler->getReferenceList($id));
			}
			return $elementList;
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IElement $element) {
			return $this->getProtocolHandler($element)->element($element);
		}
	}
