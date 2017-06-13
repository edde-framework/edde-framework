<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Http\LazyHostUrlTrait;
	use Edde\Api\Log\LazyLogServiceTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\IPacket;
	use Edde\Api\Protocol\IProtocolHandler;
	use Edde\Api\Protocol\IProtocolService;

	class ProtocolService extends AbstractProtocolHandler implements IProtocolService {
		use LazyContainerTrait;
		use LazyHostUrlTrait;
		use LazyLogServiceTrait;
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
			foreach ($this->elementQueue->getQueueList() as $element) {
				try {
					/** @var $response IElement */
					if (($response = $this->execute($element)) instanceof IElement) {
						$this->elementQueue->addReference($response);
					}
				} catch (\Exception $exception) {
					$this->logService->exception($exception);
				}
			}
			$this->elementQueue->clearQueue();
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
		public function createQueuePacket(string $scope, array $tagList = null): IPacket {
			$packet = $this->createPacket();
			$packet->setScope($scope)->setTagList($tagList)->setElementList('elements', iterator_to_array($this->getQueueList($scope, $tagList)));
			return $packet;
		}

		/**
		 * @inheritdoc
		 */
		public function createPacket(string $origin = null): IPacket {
			return new Packet($origin ?: $this->hostUrl->getAbsoluteUrl());
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
