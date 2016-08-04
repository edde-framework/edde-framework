<?php
	declare(strict_types = 1);

	namespace Edde\Api\Container;

	use Edde\Api\Callback\IParameter;

	/**
	 * Interface for variaus factory implementations for a dependency container.
	 */
	interface IFactory {
		/**
		 * get name of this factory; this should usually by name of interface
		 *
		 * @return string
		 */
		public function getName();

		/**
		 * switch singleton flag of this factory; should be used only in the configuration time
		 *
		 * @param bool $singleton
		 *
		 * @return $this
		 */
		public function setSingleton($singleton);

		/**
		 * is result of this factory singleton?
		 *
		 * @return bool
		 */
		public function isSingleton();

		/**
		 * switch cloneable flag; should be set only in the configuration time
		 *
		 * @param bool $cloneable
		 *
		 * @return $this
		 */
		public function setCloneable($cloneable);

		/**
		 * is result of this factory clone?
		 *
		 * @return bool
		 */
		public function isCloneable();

		/**
		 * return list of required parameters for this factory
		 *
		 * @return IParameter[]
		 */
		public function getParameterList();

		/**
		 * callback of this method should be called when instance of this factory is created
		 *
		 * @param callable $callback
		 *
		 * @return $this
		 */
		public function onSetup(callable $callback);

		/**
		 * create instance from this factory; container should used for injecting dependencies
		 *
		 * @param string $name
		 * @param array $parameterList
		 * @param IContainer $container
		 *
		 * @return mixed
		 */
		public function create($name, array $parameterList, IContainer $container);
	}
