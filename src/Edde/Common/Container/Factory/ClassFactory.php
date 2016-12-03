<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container\Factory;

	use Edde\Common\Reflection\ReflectionUtils;
	use ReflectionMethod;

	/**
	 * If the class exists, instance is created.
	 */
	class ClassFactory extends AbstractFactory {
		protected $parameterList;
		protected $injectList;
		protected $lazyInjectList;

		public function canHandle(string $canHandle): bool {
			return class_exists($canHandle) && interface_exists($canHandle) === false;
		}

		/**
		 * @inheritdoc
		 */
		public function getMandatoryList(string $name): array {
			if (isset($this->parameterList[$name]) === false) {
				$this->parameterList[$name] = ReflectionUtils::getParameterList($name);
			}
			return $this->parameterList[$name];
		}

		public function getInjectList(string $name): array {
			if (isset($this->injectList[$name]) === false) {
				$this->injectList[$name] = [];
				foreach (ReflectionUtils::getMethodList($name, ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
					/** @noinspection NotOptimalIfConditionsInspection */
					if (strlen($name = $reflectionMethod->getName()) > 6 && strpos($name, 'inject', 0) === 0) {
						$this->injectList[$name] = $reflectionMethod;
						foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
						}
					}
				}
			}
			return $this->injectList;
		}

		public function getLazyInjectList(string $name): array {
			if ($this->lazyInjectList === null) {
				$this->lazyInjectList = [];
				foreach (ReflectionUtils::getMethodList($name, ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
					/** @noinspection NotOptimalIfConditionsInspection */
					if (strlen($name = $reflectionMethod->getName()) > 6 && strpos($name, 'lazy', 0) === 0) {
						$this->lazyInjectList[$name] = $reflectionMethod;
						foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
						}
					}
				}
			}
			return $this->lazyInjectList;
		}
	}
