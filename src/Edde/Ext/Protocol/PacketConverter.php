<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol;

	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\Protocol\IPacket;
	use Edde\Common\Converter\AbstractConverter;

	class PacketConverter extends AbstractConverter {
		use LazyConverterManagerTrait;

		/**
		 * PacketConverter constructor.
		 */
		public function __construct() {
			$this->register(IPacket::class, [
				'application/json',
			]);
		}

		/**
		 * @inheritdoc
		 *
		 * @param IPacket $content
		 */
		public function convert($content, string $mime, string $target) {
			return $this->converterManager->convert($content->packet(), 'object', [$target])
				->convert();
		}
	}
