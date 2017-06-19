<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Http\LazyHostUrlTrait;
	use Edde\Api\Log\LazyLogServiceTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\IPacket;
	use Edde\Api\Protocol\IProtocolHandler;
	use Edde\Api\Protocol\IProtocolService;

	class ProtocolService extends AbstractProtocolHandler implements IProtocolService {
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
			foreach ($this->elementQueue->getElementList() as $element) {
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
		public function createQueuePacket(): IPacket {
			$packet = $this->createPacket();
			$packet->setElementList('elements', iterator_to_array($this->elementQueue));
			$packet->setElementList('references', $this->elementQueue->getReferenceList());
			return $packet;
		}

		/**
		 * @inheritdoc
		 */
		public function createPacket(IElement $reference = null, string $origin = null): IPacket {
			$packet = new Packet($origin ?: $this->hostUrl->getAbsoluteUrl());
			if ($reference) {
				/**
				 * set the Element reference (this is a bit different than "addReference()")
				 */
				$packet->setReference($reference);
				/**
				 * add the request to the list of references in Packet
				 */
				$packet->reference($reference);
			}
			return $packet;
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
