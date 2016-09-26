<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Container\IContainer;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Usable\UsableTrait;

	/**
	 * Magical implementation of callback search mechanism based on "class exists".
	 */
	class CascadeFactory extends ClassFactory {
		use UsableTrait;
		/**
		 * @var array
		 */
		protected $sourceList;
		/**
		 * @var callable
		 */
		protected $parameterCallback;
		protected $parameters = [];

		/**
		 * "The primary purpose of the DATA statement is to give names to constants; instead of referring to pi as 3.141592653589793 at every appearance, the variable PI can be given that value with a DATA statement and used instead of the longer form of the constant. This also simplifies modifying the program, should the value of pi change."
		 *
		 * -- FORTRAN manual for Xerox Computers
		 *
		 * @param array $sourceList
		 * @param callable $parameterCallback
		 */
		public function __construct(array $sourceList, callable $parameterCallback = null) {
			parent::__construct();
			$this->sourceList = $sourceList;
			$this->parameterCallback = $parameterCallback;
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
			$this->use();
			$parameterList = $this->parameters;
			if (interface_exists($name)) {
				$name = substr(StringUtils::extract($name, '\\', -1), 1);
			}
			$parameterList['class'] = $name;
			$parameters = [];
			foreach ($parameterList as $k => $v) {
				$parameters['{' . $k . '}'] = $v;
			}
			$from = array_keys($parameters);
			$to = array_values($parameters);
			foreach ($this->sourceList as $source) {
				if (class_exists($source = str_replace($from, $to, $source))) {
					return $source;
				}
			}
			return null;
		}

		/**
		 * @inheritdoc
		 */
		public function getParameterList(string $name = null): array {
			return parent::getParameterList($this->discover($name));
		}

		/**
		 * @inheritdoc
		 */
		public function factory(string $name, array $parameterList, IContainer $container) {
			return parent::factory($this->discover($name), $parameterList, $container);
		}

		protected function prepare() {
			$this->parameters = $this->parameterCallback ? call_user_func($this->parameterCallback) : [];
		}
	}
