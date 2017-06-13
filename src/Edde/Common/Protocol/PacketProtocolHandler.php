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
		public function queue(IElement $element) {
			parent::queue($element);
			return $this->protocolService->createPacket()->reference($element)->setReference($element);
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IElement $element) {
			$packet = $this->protocolService->createPacket();
			/**
			 * set the Element reference (this is a bit different than "addReference()"
			 */
			$packet->setReference($element);
			/**
			 * add the request to the list of references in Packet
			 */
			$packet->reference($element);
			foreach ($element->getElementList('elements') as $node) {
				/** @var $response IElement */
				if (($response = $this->protocolService->element($node)) instanceof IElement) {
					$packet->element($response->setReference($node));
					$packet->reference($node);
				}
			}
			return $packet;
		}
	}
