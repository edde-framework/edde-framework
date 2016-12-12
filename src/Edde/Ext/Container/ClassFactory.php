<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Container;

	use Edde\Api\Container\ContainerException;
	use Edde\Api\Container\IDependency;
	use Edde\Common\Container\AbstractFactory;
	use Edde\Common\Container\Dependency;
	use Edde\Common\Reflection\ReflectionUtils;

	class ClassFactory extends AbstractFactory {
		public function canHandle(string $dependency): bool {
			return class_exists($dependency) && interface_exists($dependency) === false;
		}

		public function dependency(string $dependency = null): IDependency {
			$injectList = [];
			$lazyList = [];
			foreach (ReflectionUtils::getMethodList($dependency) as $reflectionMethod) {
				$reflectionClass = $reflectionMethod->getDeclaringClass();
				/** @noinspection NotOptimalIfConditionsInspection */
				if (strlen($name = $reflectionMethod->getName()) > 6 && strpos($name, 'inject', 0) === 0) {
					if ($reflectionMethod->isPublic() === false) {
						throw new ContainerException(sprintf('Inject method [%s::%s()] must be public.', $reflectionClass->getName(), $reflectionMethod->getName()));
					}
					$inject = [];
					foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
						if ($reflectionClass->hasProperty($name = $reflectionParameter->getName()) === false) {
							throw new ContainerException(sprintf('Class [%s] must have property [$%s] of the same name as parameter in inject method [%s::%s(..., %s$%s, ...)].', $reflectionClass->getName(), $name, $reflectionClass->getName(), $reflectionMethod->getName(), ($class = $reflectionParameter->getClass()) ? $class->getName() . ' ' : null, $name));
						}
						$reflectionProperty = $reflectionClass->getProperty($name);
						$reflectionProperty->setAccessible(true);
						$inject[$name] = [
							$reflectionParameter,
							$reflectionProperty,
						];
					}
					$injectList[$reflectionMethod->getName()] = $inject;
				}
				/** @noinspection NotOptimalIfConditionsInspection */
				if (strlen($name = $reflectionMethod->getName()) > 6 && strpos($name, 'lazy', 0) === 0) {
					if ($reflectionMethod->isPublic() === false) {
						throw new ContainerException(sprintf('Lazy method [%s::%s()] must be public.', $reflectionClass->getName(), $reflectionMethod->getName()));
					}
					$lazy = [];
					foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
						if ($reflectionClass->hasProperty($name = $reflectionParameter->getName()) === false) {
							throw new ContainerException(sprintf('Class [%s] must have property [$%s] of the same name as parameter in lazy inject method [%s::%s(..., %s$%s, ...)].', $reflectionClass->getName(), $name, $reflectionClass->getName(), $reflectionMethod->getName(), ($class = $reflectionParameter->getClass()) ? $class->getName() . ' ' : null, $name));
						}
						$reflectionProperty = $reflectionClass->getProperty($name);
						$reflectionProperty->setAccessible(true);
						$lazy[$name] = [
							$reflectionParameter,
							$reflectionProperty,
						];
					}
					$lazyList[$reflectionMethod->getName()] = $lazy;
				}
			}
			return new Dependency(ReflectionUtils::getParameterList($dependency), $injectList, $lazyList);
		}

		public function execute(array $parameterList, string $name = null) {
			return new $name(...$parameterList);
		}
	}
