<?php
	declare(strict_types=1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Application\LazyContextTrait;

	class CascadeFactory extends AbstractDiscoveryFactory {
		use LazyContextTrait;

		/**
		 * @inheritdoc
		 */
		protected function discover(string $name): array {
			return $this->context->cascade('\\', $name);
		}
	}
