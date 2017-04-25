<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol;

	use Edde\Api\Protocol\IPacket;
	use Edde\Common\Application\Response;

	class PacketResponse extends Response {
		public function __construct(IPacket $packet, array $targetList = null) {
			parent::__construct($packet, IPacket::class, $targetList);
		}
	}
