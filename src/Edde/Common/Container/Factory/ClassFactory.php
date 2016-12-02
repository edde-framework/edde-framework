<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Callback\IParameter;
	use Edde\Common\Callback\CallbackUtils;

	/**
	 * If the class exists, instance is created.
	 */
	class ClassFactory extends AbstractFactory {
		/**
		 * @var IParameter[]
		 */
		protected $parameterList;

		public function canHandle(string $canHandle): bool {
			return class_exists($canHandle) && interface_exists($canHandle) === false;
		}

		public function getMandatoryList(string $name): iterable {
			if ($this->parameterList === null) {
				$this->parameterList = CallbackUtils::getParameterList($name);
			}
			return $this->parameterList;
		}
	}
