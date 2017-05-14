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
		protected $handle = [];

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
		public function canHandle(IElement $element): bool {
			if ($element->isType('packet')) {
				return true;
			} else if (isset($this->handle[$type = $element->getType()])) {
				return $this->handle[$type]->canHandle($element);
			}
			foreach ($this->protocolHandlerList as $protocolHandler) {
				$protocolHandler->setup();
				if ($protocolHandler->canHandle($element)) {
					$this->handle[$type] = $protocolHandler;
					return true;
				}
			}
			return false;
		}

		/**
		 * @inheritdoc
		 */
		public function queue(IElement $element): IProtocolHandler {
			$this->check($element);
			if ($element->isType('packet')) {
				return parent::queue($element);
			}
			if (isset($this->handle[$type = $element->getType()])) {
				$this->handle[$type]->queue($element);
				return $this;
			}
			foreach ($this->protocolHandlerList as $protocolHandler) {
				if ($protocolHandler->canHandle($element)) {
					$this->handle[$type] = $protocolHandler;
					$protocolHandler->queue($element);
					return $this;
				}
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function element(IElement $element) {
			$this->check($element);
			if ($element->isType('packet') && $element->isAsync()) {
				$this->queue($element);
				$packet = new Packet($this->hostUrl->getAbsoluteUrl());
				$packet->setReference($element);
				$packet->addElement('references', $element);
				return $packet;
			}
			return $this->execute($element);
		}

		/**
		 * @inheritdoc
		 */
		public function dequeue(string $scope = null, array $tagList = null): IProtocolHandler {
			foreach ($this->protocolHandlerList as $protocolHandler) {
				$protocolHandler->dequeue($scope, $tagList);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function iterate(string $scope = null, array $tagList = null) {
			foreach ($this->protocolHandlerList as $protocolHandler) {
				foreach ($protocolHandler->iterate($scope, $tagList) as $element) {
					yield $element;
				}
			}
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IElement $element) {
			if (($element->isType('packet'))) {
				return $this->request($element);
			}
			if (isset($this->handle[$type = $element->getType()])) {
				return $this->handle[$type]->execute($element);
			}
			foreach ($this->protocolHandlerList as $protocolHandler) {
				$protocolHandler->setup();
				if ($protocolHandler->canHandle($element)) {
					$this->handle[$type] = $protocolHandler;
					return $protocolHandler->execute($element);
				}
			}
			throw new NoHandlerException(sprintf('Element [%s (%s)] has no available handler.', $type, get_class($element)));
		}

		protected function request(IElement $element): IElement {
			$packet = new Packet($this->hostUrl->getAbsoluteUrl());
			/**
			 * set the Element reference (this is a bit different than "addReference()"
			 */
			$packet->setReference($element);
			/**
			 * add the request to the list of references in Packet
			 */
			$packet->addElement('references', $element);
			foreach ($element->getElementList('elements') as $node) {
				/** @var $response IElement */
				if (($response = $this->execute($node)) instanceof IElement) {
					$packet->addElement('elements', $response->setReference($node));
					$packet->addElement('references', $node);
				}
			}
			return $packet;
		}
	}
