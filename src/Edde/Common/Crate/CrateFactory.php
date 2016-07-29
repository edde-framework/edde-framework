<?php
	namespace Edde\Common\Crate;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Common\AbstractObject;

	class CrateFactory extends AbstractObject implements ICrateFactory {
		/**
		 * @var IContainer
		 */
		protected $container;

		/**
		 * @param IContainer $container
		 */
		public function __construct(IContainer $container) {
			$this->container = $container;
		}

		public function build(array $crateList) {
		}
	}
