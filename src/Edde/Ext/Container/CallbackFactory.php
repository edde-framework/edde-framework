<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Container;

	use Edde\Api\Container\IDependency;
	use Edde\Common\Container\AbstractFactory;
	use Edde\Common\Container\Dependency;
	use Edde\Common\Reflection\ReflectionUtils;

	class CallbackFactory extends AbstractFactory {
		/**
		 * @var callable
		 */
		protected $callback;
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @param string $name
		 * @param callable $callback
		 */
		public function __construct(callable $callback, string $name = null) {
			$this->callback = $callback;
			$this->name = $name;
		}

		/**
		 * @inheritdoc
		 */
		public function canHandle($dependency): bool {
			return is_string($dependency) && $dependency === $this->name;
		}

		/**
		 * @inheritdoc
		 */
		public function dependency($dependency): IDependency {
			if (($source = $this->load($cacheId = ('dependency/' . $dependency))) === null || $dependency === null) {
				$this->save($cacheId, $source = new Dependency(ReflectionUtils::getParameterList($this->callback), [], []));
			}
			return $source;
		}

		public function execute(array $parameterList, string $name = null) {
			return call_user_func_array($this->callback, $parameterList);
		}
	}
