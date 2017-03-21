<?php
	declare(strict_types=1);

	namespace Edde\Ext\Application;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Resource\LazyResourceManagerTrait;
	use Edde\Common\Application\AbstractContext;
	use Edde\LazyFrameworkTrait;

	class FrameworkContext extends AbstractContext implements ILazyInject {
		use LazyFrameworkTrait;
		use LazyResourceManagerTrait;

		/**
		 * @inheritdoc
		 */
		public function getId(): string {
			return $this->framework->getVersionString();
		}
	}
