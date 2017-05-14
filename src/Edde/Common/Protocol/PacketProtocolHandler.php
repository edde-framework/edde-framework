<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Http\LazyHostUrlTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\LazyProtocolServiceTrait;

	class PacketProtocolHandler extends AbstractProtocolHandler {
		use LazyProtocolServiceTrait;
		use LazyHostUrlTrait;

		/**
		 * @inheritdoc
		 */
		public function canHandle(IElement $element): bool {
			return $element->isType('packet');
		}

		/**
		 * @inheritdoc
		 */
		public function element(IElement $element) {
			$this->check($element);
			if ($element->isAsync()) {
				$this->queue($element);
				$packet = new Packet($this->hostUrl->getAbsoluteUrl());
				$packet->setReference($element);
				$packet->reference($element);
				return $packet;
			}
			return $this->execute($element);
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IElement $element) {
			$packet = new Packet($this->hostUrl->getAbsoluteUrl());
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
				if (($response = $this->protocolService->execute($node)) instanceof IElement) {
					$packet->element($response->setReference($node));
					$packet->reference($node);
				}
			}
			return $packet;
		}
	}
