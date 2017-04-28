<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\IPacket;
	use Edde\Api\Protocol\IProtocolHandler;
	use Edde\Api\Protocol\IProtocolService;

	class ProtocolService extends AbstractProtocolHandler implements IProtocolService {
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
			return in_array($element->getType(), [
				'packet',
				'request',
				'message',
				'event',
			]);
		}

		/**
		 * @inheritdoc
		 */
		protected function element(IElement $element) {
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
