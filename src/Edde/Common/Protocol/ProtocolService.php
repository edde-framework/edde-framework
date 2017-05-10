<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Http\LazyHostUrlTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\IPacket;
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
			if ($element instanceof IPacket) {
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
		public function element(IElement $element) {
			$this->check($element);
			if ($element instanceof IPacket && $element->isAsync()) {
				$this->queue($element);
				$packet = $this->container->create(IPacket::class);
				$packet->setReference($element);
				$packet->addReference($element);
				return $packet;
			}
			return $this->execute($element);
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IElement $element) {
			if ($element instanceof IPacket) {
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

		protected function request(IPacket $request): IPacket {
			$packet = $this->container->create(IPacket::class);
			/**
			 * set the Element reference (this is a bit different than "addReference()"
			 */
			$packet->setReference($request);
			/**
			 * add the request to the list of references in Packet
			 */
			$packet->addReference($request);
			foreach ($request->getElementList() as $element) {
				/** @var $response IElement */
				if (($response = $this->execute($element)) instanceof IElement) {
					$packet->addElement($response->setReference($element));
					$packet->addReference($element);
				}
			}
			return $packet;
		}

		/**
		 * @inheritdoc
		 */
		public function packet(string $scope = null, array $tagList = null, IPacket $packet = null): IPacket {
			$packet = parent::packet($scope, $tagList, $packet);
			foreach ($this->protocolHandlerList as $protocolHandler) {
				$protocolHandler->packet($scope, $tagList, $packet);
			}
			return $packet;
		}
	}
