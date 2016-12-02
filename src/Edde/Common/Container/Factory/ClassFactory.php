<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container\Factory;

	/**
	 * If the class exists, instance is created.
	 */
	class ClassFactory extends AbstractFactory {
		public function canHandle(string $canHandle): bool {
			return class_exists($canHandle) && interface_exists($canHandle) === false;
		}
	}
