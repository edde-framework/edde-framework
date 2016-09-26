<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\ILazyInject;
	use Edde\Common\Callback\CallbackUtils;

	/**
	 * Magical implementation of callback search mechanism based on "class exists".
	 */
	class CascadeFactory extends ClassFactory implements ILazyInject {
		/**
		 * @var callable
		 */
		protected $source;
		/**
		 * @var IContainer
		 */
		protected $container;

		/**
		 * "The primary purpose of the DATA statement is to give names to constants; instead of referring to pi as 3.141592653589793 at every appearance, the variable PI can be given that value with a DATA statement and used instead of the longer form of the constant. This also simplifies modifying the program, should the value of pi change."
		 *
		 * -- FORTRAN manual for Xerox Computers
		 *
		 * @param callable $source
		 */
		public function __construct(callable $source) {
			parent::__construct();
			$this->source = $source;
		}

		/**
		 * @param IContainer $container
		 */
		public function lazyContainer(IContainer $container) {
			$this->container = $container;
		}

		/**
		 * @inheritdoc
		 */
		public function canHandle(string $name): bool {
			if ($discover = $this->discover($name)) {
				return parent::canHandle($discover);
			}
			return false;
		}

		/**
		 * @param string $name
		 *
		 * @return string|null
		 */
		protected function discover(string $name) {
			/** @noinspection ForeachSourceInspection */
			foreach ($this->container->call($this->source, $name) as $source) {
				if (class_exists($source)) {
					return $source;
				}
			}
			return null;
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		public function getParameterList(string $name = null): array {
			return CallbackUtils::getParameterList($this->discover($name));
		}

		/**
		 * @inheritdoc
		 */
		public function factory(string $name, array $parameterList, IContainer $container) {
			return parent::factory($this->discover($name), $parameterList, $container);
		}
	}