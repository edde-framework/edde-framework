<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Html\IHtmlTemplate;
	use Edde\Common\Template\AbstractTemplate;

	abstract class AbstractHtmlTemplate extends AbstractTemplate implements IHtmlTemplate, ILazyInject {
		/**
		 * @var IContainer
		 */
		protected $container;

		public function lazyContainer(IContainer $container) {
			$this->container = $container;
		}
	}
