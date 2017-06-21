<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\LazyProtocolServiceTrait;

	class PacketProtocolHandler extends AbstractProtocolHandler {
		use LazyProtocolServiceTrait;

		/**
		 * @inheritdoc
		 */
		public function canHandle(IElement $element): bool {
			return $element->isType('packet');
		}

		/**
		 * @inheritdoc
		 */
		public function onExecute(IElement $element) {
			$packet = $this->protocolService->createPacket($element);
			foreach ($element->getElementList('elements') as $node) {
				/** @var $response IElement */
				if (($response = $this->protocolService->execute($node)) instanceof IElement) {
					$packet->element($response->setReference($node));
					$packet->reference($node);
				}
			}
			return $packet;
		}

		/**
		 * @inheritdoc
		 */
		protected function onQueue(IElement $element) {
			return $this->protocolService->createPacket()->reference($element)->setReference($element);
		}
	}
