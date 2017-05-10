<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol;

	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\Protocol\IPacket;
	use Edde\Api\Protocol\LazyProtocolServiceTrait;
	use Edde\Common\Converter\AbstractConverter;

	class PacketConverter extends AbstractConverter {
		use LazyConverterManagerTrait;
		use LazyProtocolServiceTrait;

		/**
		 * PacketConverter constructor.
		 */
		public function __construct() {
			$this->register(IPacket::class, [
				'application/json',
				'*/*',
			]);
			$this->register(['stream+application/json'], IPacket::class);
		}

		/**
		 * @inheritdoc
		 *
		 * @param IPacket $content
		 */
		public function convert($content, string $mime, string $target) {
			if ($target === IPacket::class) {
				return $this->protocolService->createPacket($this->converterManager->convert($content, $mime, ['object'])->convert());
			}
			return $this->converterManager->convert($content->packet(), 'object', [$target])->convert();
		}
	}
