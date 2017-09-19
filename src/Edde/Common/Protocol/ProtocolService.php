<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\IProtocolHandler;
	use Edde\Api\Protocol\IProtocolService;
	use Edde\Common\Protocol\Exception\UnsupportedElementException;

	class ProtocolService extends AbstractProtocolHandler implements IProtocolService {
		use Edde\Api\Http\Inject\LazyHostUrlTrait;
		use Edde\Api\Log\Inject\LazyLogServiceTrait;
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
		public function createPacket(IElement $reference = null, string $origin = null): IElement {
			$packet = new Packet($origin ?: $this->hostUrl->getAbsoluteUrl());
			$packet->setReference($reference);
			return $packet;
		}

		/**
		 * @inheritdoc
		 */
		public function canHandle(IElement $element): bool {
			return $this->getProtocolHandler($element)
				->canHandle($element);
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IElement $element) {
			try {
				return $response = $this->getProtocolHandler($element)
					->execute($element);
			} catch (\Throwable $exception) {
				$response = new Error(-102, $exception->getMessage());
				$response->setException(get_class($exception));
				$response->setReference($element);
				$this->logService->exception($exception);
				return $response;
			} finally {
				if ($element->getMeta('store', false)) {
					$this->elementStore->save($element);
					if (isset($response) && $response instanceof IElement) {
						$this->elementStore->save($response);
					}
				}
			}
		}

		protected function getProtocolHandler(IElement $element): IProtocolHandler {
			if (isset($this->handleList[$type = $element->getType()])) {
				return $this->handleList[$type];
			}
			foreach ($this->protocolHandlerList as $protocolHandler) {
				$protocolHandler->setup();
				if ($protocolHandler->canHandle($element)) {
					return $this->handleList[$type] = $protocolHandler;
				}
			}
			throw new UnsupportedElementException(sprintf('There is no protocol handler for the given element [%s].', $type));
		}
	}
