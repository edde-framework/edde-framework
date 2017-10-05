<?php
	declare(strict_types=1);
	namespace Edde\Api\Container;

	interface IParameter {
		/**
		 * return name of a parameter
		 *
		 * @return string
		 */
		public function getName(): string;

		/**
		 * return class name of the parameter
		 *
		 * @return null|string
		 */
		public function getClass(): string;
	}
