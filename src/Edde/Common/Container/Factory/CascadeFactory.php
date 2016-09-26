<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Container\IContainer;
	use Edde\Common\Usable\UsableTrait;

	/**
	 * Magical implementation of callback search mechanism based on "class exists".
	 */
	class CascadeFactory extends ReflectionFactory {
		use UsableTrait;
		/**
		 * @var array
		 */
		protected $sourceList;
		/**
		 * @var callable
		 */
		protected $parameterCallback;

		/**
		 * "The primary purpose of the DATA statement is to give names to constants; instead of referring to pi as 3.141592653589793 at every appearance, the variable PI can be given that value with a DATA statement and used instead of the longer form of the constant. This also simplifies modifying the program, should the value of pi change."
		 *
		 * -- FORTRAN manual for Xerox Computers
		 *
		 * @param string $name
		 * @param string $class
		 * @param array $sourceList
		 * @param callable $parameterCallback
		 */
		public function __construct(string $name, string $class, array $sourceList, callable $parameterCallback = null) {
			parent::__construct($name, $class, false, false);
			$this->class = $class;
			$this->sourceList = $sourceList;
			$this->parameterCallback = $parameterCallback;
		}

		/**
		 * @inheritdoc
		 */
		public function getParameterList() {
			$this->use();
			return parent::getParameterList();
		}

		/**
		 * @inheritdoc
		 */
		public function factory(string $name, array $parameterList, IContainer $container) {
			$this->use();
			return parent::factory($name, $parameterList, $container);
		}

		/**
		 * @inheritdoc
		 */
		protected function prepare() {
			$parameterList = [];
			foreach (array_merge($this->parameterCallback ? call_user_func($this->parameterCallback) : [], ['class' => $this->class]) as $k => $v) {
				$parameterList['{' . $k . '}'] = $v;
			}
			$from = array_keys($parameterList);
			$to = array_values($parameterList);
			foreach ($this->sourceList as &$source) {
				if (class_exists($source = str_replace($from, $to, $source))) {
					$this->class = $source;
					break;
				}
			}
			unset($source);
		}
	}
