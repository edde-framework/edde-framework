<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Container\IDependency;
	use Edde\Common\AbstractObject;

	class Dependency extends AbstractObject implements IDependency {
		protected $parameterList = [];
		protected $injectList = [];
		protected $lazyList = [];

		/**
		 * @param array $parameterList
		 * @param array $injectList
		 * @param array $lazyList
		 */
		public function __construct(array $parameterList, array $injectList, array $lazyList) {
			$this->parameterList = $parameterList;
			$this->injectList = $injectList;
			$this->lazyList = $lazyList;
		}

		public function getParameterList(): array {
			return $this->parameterList;
		}

		public function getInjectList(): array {
			return $this->injectList;
		}

		public function getLazyList(): array {
			return $this->lazyList;
		}
	}
