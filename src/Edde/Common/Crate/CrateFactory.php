<?php
	namespace Edde\Common\Crate;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crate\CrateException;
	use Edde\Api\Crate\ICrate;
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

		public function create($name, ...$parameterList) {
			if (($crate = $this->container->create($name, ...$parameterList)) instanceof ICrate === false) {
				throw new CrateException(sprintf('Requested crate [%s] is not instance of [%s].', $name, ICrate::class));
			}
			$this->fill($crate);
			return $crate;
		}

		public function fill(ICrate $crate) {
			$schema = $crate->getSchema();
			foreach ($schema->getPropertyList() as $property) {
				$crate->addValue(new Value($property));
			}
			return $this;
		}
	}
