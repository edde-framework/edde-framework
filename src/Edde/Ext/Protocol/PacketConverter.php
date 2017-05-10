<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Api\Protocol\IPacket;
	use Edde\Common\Converter\AbstractConverter;

	class PacketConverter extends AbstractConverter {
		use LazyConverterManagerTrait;
		use LazyContainerTrait;

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
				/** @var $packet IPacket */
				$packet = $this->container->create(IPacket::class);
				/**
				 * keep this talkative to let IDE know about all methods on IPacket interface
				 */
				$packet->from($this->converterManager->convert($content, $mime, ['object'])->convert());
				return $packet;
			}
			return $this->converterManager->convert($content->packet(), 'object', [$target])->convert();
		}
	}
