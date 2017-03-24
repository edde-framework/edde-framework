<?php
	declare(strict_types=1);

	namespace Edde\Common\Cache;

	trait CacheableTrait {
		public function __sleep() {
			static $allowed = [
				\stdClass::class,
				\SplStack::class,
			];
			$reflectionClass = new \ReflectionClass($this);
			$diff = [];
			foreach ($reflectionClass->getProperties() as $reflectionProperty) {
				$name = $reflectionProperty->getName();
				if (isset($this->{$name}) === false) {
					continue;
				} else if (strpos($doc = is_string($doc = $reflectionProperty->getDocComment()) ? $doc : '', '@no-cache') !== false) {
					$diff[] = $name;
				} else if (is_object($this->{$name}) && in_array($class = get_class($this->{$name}), $allowed) === false) {
					$diff[] = $name;
				}
			}
			return array_diff(array_keys(get_object_vars($this)), $diff);
		}
	}
