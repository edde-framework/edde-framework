<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Container;

	use Edde\Api\Container\ContainerException;
	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IDependency;
	use Edde\Api\Container\ILazyInject;
	use Edde\Common\Container\AbstractFactory;
	use Edde\Common\Container\Dependency;
	use Edde\Common\Reflection\ReflectionParameter;
	use Edde\Common\Reflection\ReflectionUtils;

	class ClassFactory extends AbstractFactory {
		/**
		 * @inheritdoc
		 */
		public function canHandle(IContainer $container, string $dependency): bool {
			return class_exists($dependency) && interface_exists($dependency) === false;
		}

		/**
		 * @inheritdoc
		 * @throws ContainerException
		 */
		public function dependency(IContainer $container, string $dependency = null): IDependency {
			$injectList = [];
			$lazyList = [];
			foreach (ReflectionUtils::getMethodList($dependency) as $reflectionMethod) {
				$reflectionClass = $reflectionMethod->getDeclaringClass();
				/** @noinspection NotOptimalIfConditionsInspection */
				if (strlen($name = $reflectionMethod->getName()) > 6 && strpos($name, 'inject', 0) === 0) {
					if ($reflectionMethod->isPublic() === false) {
						throw new ContainerException(sprintf('Inject method [%s::%s()] must be public.', $reflectionClass->getName(), $reflectionMethod->getName()));
					}
					foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
						if ($reflectionClass->hasProperty($name = $reflectionParameter->getName()) === false) {
							throw new ContainerException(sprintf('Class [%s] must have property [$%s] of the same name as parameter in inject method [%s::%s(..., %s$%s, ...)].', $reflectionClass->getName(), $name, $reflectionClass->getName(), $reflectionMethod->getName(), ($class = $reflectionParameter->getClass()) ? $class->getName() . ' ' : null, $name));
						}
						$reflectionProperty = $reflectionClass->getProperty($name);
						$reflectionProperty->setAccessible(true);
						$injectList[] = new ReflectionParameter($reflectionProperty->getName(), false, ($class = $reflectionParameter->getClass()) ? $class->getName() : $reflectionParameter->getName());
					}
				}
				/** @noinspection NotOptimalIfConditionsInspection */
				if ($reflectionClass->implementsInterface(ILazyInject::class) && strlen($name = $reflectionMethod->getName()) > 6 && strpos($name, 'lazy', 0) === 0) {
					if ($reflectionMethod->isPublic() === false) {
						throw new ContainerException(sprintf('Lazy method [%s::%s()] must be public.', $reflectionClass->getName(), $reflectionMethod->getName()));
					}
					foreach ($reflectionMethod->getParameters() as $reflectionParameter) {
						if ($reflectionClass->hasProperty($name = $reflectionParameter->getName()) === false) {
							throw new ContainerException(sprintf('Class [%s] must have property [$%s] of the same name as parameter in lazy inject method [%s::%s(..., %s$%s, ...)].', $reflectionClass->getName(), $name, $reflectionClass->getName(), $reflectionMethod->getName(), ($class = $reflectionParameter->getClass()) ? $class->getName() . ' ' : null, $name));
						}
						$reflectionProperty = $reflectionClass->getProperty($name);
						$reflectionProperty->setAccessible(true);
						$lazyList[] = new ReflectionParameter($reflectionProperty->getName(), false, ($class = $reflectionParameter->getClass()) ? $class->getName() : $reflectionParameter->getName());
					}
				}
			}
			$parameterList = [];
			foreach (ReflectionUtils::getParameterList($dependency) as $reflectionParameter) {
				$parameterList[] = new ReflectionParameter($reflectionParameter->getName(), $reflectionParameter->isOptional(), ($class = $reflectionParameter->getClass()) ? $class->getName() : null);
			}
			return new Dependency($parameterList, $injectList, $lazyList);
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IContainer $container, array $parameterList, string $name = null) {
			if (empty($parameterList)) {
				return new $name();
			}
			return new $name(...$parameterList);
		}
	}
