<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Callback\IParameter;
	use Edde\Common\Reflection\ReflectionUtils;

	/**
	 * If the class exists, instance is created.
	 */
	class ClassFactory extends AbstractFactory {
		/**
		 * @var IParameter[]
		 */
		protected $parameterList;
		protected $lazyInjectList;

		public function canHandle(string $canHandle): bool {
			return class_exists($canHandle) && interface_exists($canHandle) === false;
		}

		public function getMandatoryList(string $name): array {
			if ($this->parameterList === null) {
				$this->parameterList = ReflectionUtils::getParameterList($name);
			}
			return $this->parameterList;
		}

		public function getLazyInjectList(): array {
		}
	}
