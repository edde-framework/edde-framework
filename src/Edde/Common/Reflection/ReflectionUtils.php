<?php
	declare(strict_types = 1);

	namespace Edde\Common\Reflection;

	use Edde\Api\Reflection\ReflectionException;
	use Edde\Common\AbstractObject;

	/**
	 * Set of tools for simplier reflection manipulation.
	 */
	class ReflectionUtils extends AbstractObject {
		/**
		 * bypass property visibility and set a given value
		 *
		 * @param $object
		 * @param $property
		 * @param $value
		 *
		 * @throws ReflectionException
		 */
		static public function setProperty($object, $property, $value) {
			try {
				$reflectionClass = new \ReflectionClass($object);
				$reflectionProperty = $reflectionClass->getProperty($property);
				$reflectionProperty->setAccessible(true);
				$reflectionProperty->setValue($object, $value);
			} catch (\ReflectionException $exception) {
				throw new ReflectionException(sprintf('Property [%s::$%s] does not exists.', get_class($object), $property));
			}
		}
	}
