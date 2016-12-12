<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Container;

	use Edde\Api\Container\IDependency;
	use Edde\Common\Container\AbstractFactory;
	use Edde\Common\Container\Dependency;
	use Edde\Common\Reflection\ReflectionUtils;

	class CallbackFactory extends AbstractFactory {
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var callable
		 */
		protected $callback;

		/**
		 * @param string $name
		 * @param callable $callback
		 */
		public function __construct(string $name, callable $callback) {
			$this->name = $name;
			$this->callback = $callback;
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
			if (($source = $this->load($cacheId = ('dependency/' . $dependency))) === null) {
				$this->save($cacheId, $source = new Dependency(ReflectionUtils::getParameterList($this->callback), [], []));
			}
			return $source;
		}

		public function getCode(): string {
			return ReflectionUtils::getCode($this->callback);
		}
	}
