<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Job\LazyJobManagerTrait;
	use Edde\Api\Protocol\IElement;
	use Edde\Api\Protocol\IPacket;
	use Edde\Api\Protocol\IProtocolManager;
	use Edde\Api\Protocol\LazyProtocolServiceTrait;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractProtocolManager extends Object implements IProtocolManager {
		use LazyProtocolServiceTrait;
		use LazyJobManagerTrait;
		use ConfigurableTrait;

		/**
		 * @inheritdoc
		 */
		public function createPacket(IElement $reference = null): IPacket {
			$packet = $this->protocolService->createPacket($reference);
			foreach ($this->jobManager->dequeue() as $element) {
				$packet->element($element);
			}
			return $packet;
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IElement $element): IElement {
			if ($element->isAsync()) {
				$this->jobManager->queue($element);
				return $this->protocolService->createPacket($element);
			}
			return $this->protocolService->execute($element);
		}
	}
