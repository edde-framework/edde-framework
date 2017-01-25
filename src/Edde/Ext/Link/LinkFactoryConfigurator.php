<?php
	declare(strict_types=1);

	namespace Edde\Ext\Link;

	use Edde\Api\Container\LazyContainerTrait;
	use Edde\Api\Link\ILinkFactory;
	use Edde\Common\Config\AbstractConfigurator;

	class LinkFactoryConfigurator extends AbstractConfigurator {
		use LazyContainerTrait;

		/**
		 * @param ILinkFactory $instance
		 */
		public function config($instance) {
		}
	}
