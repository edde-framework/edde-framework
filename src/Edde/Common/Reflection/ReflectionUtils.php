<?php
	declare(strict_types = 1);

	namespace Edde\Common\Reflection;

	use Edde\Common\AbstractObject;

	class ReflectionUtils extends AbstractObject {
		static public function setProperty($object, $property, $value) {
			$reflectionClass = new \ReflectionClass($object);
			$reflectionProperty = $reflectionClass->getProperty($property);
			$reflectionProperty->setAccessible(true);
			$reflectionProperty->setValue($object, $value);
		}
	}
