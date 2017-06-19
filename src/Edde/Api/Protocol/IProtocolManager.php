<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol;

	use Edde\Api\Config\IConfigurable;

	interface IProtocolManager extends IConfigurable {
		/**
		 * create packet with payload of all available elements and references
		 *
		 * @param IElement|null $reference
		 *
		 * @return IPacket
		 */
		public function createPacket(IElement $reference = null): IPacket;
	}
