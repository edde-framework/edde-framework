<?php
	namespace Edde\Common\Storage;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crate\ICrateFactory;
	use Edde\Api\Storage\IStorable;
	use Edde\Api\Storage\IStorableFactory;
	use Edde\Api\Storage\StorableException;
	use Edde\Common\AbstractObject;

	class StorableFactory extends AbstractObject implements IStorableFactory {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var ICrateFactory
		 */
		protected $crateFactory;

		/**
		 * @param IContainer $container
		 * @param ICrateFactory $crateFactory
		 */
		public function __construct(IContainer $container, ICrateFactory $crateFactory) {
			$this->container = $container;
			$this->crateFactory = $crateFactory;
		}

		public function create($name, ...$parameterList) {
			if (($storable = $this->container->create($name, ...$parameterList)) instanceof IStorable === false) {
				throw new StorableException(sprintf('Requested storable [%s] is not instance of [%s].', $name, IStorable::class));
			}
			$this->crateFactory->fill($storable);
			return $storable;
		}
	}
