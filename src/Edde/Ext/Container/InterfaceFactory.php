<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Container;

	/**
	 * Interface to class binding factory.
	 */
	class InterfaceFactory extends ClassFactory {
		/**
		 * @var string
		 */
		protected $interface;
		/**
		 * @var string
		 */
		protected $class;

		/**
		 * Practical thought:
		 * A husband is supposed to make his wife's panties wet, not her eyes.
		 * A wife is supposed to make her husband's dick hard, not his life...!
		 *
		 * @param string $interface
		 * @param string $class
		 */
		public function __construct($interface, $class) {
			$this->interface = $interface;
			$this->class = $class;
		}

		/**
		 * @inheritdoc
		 */
		public function canHandle(string $dependency): bool {
			return $dependency === $this->interface;
		}
	}
