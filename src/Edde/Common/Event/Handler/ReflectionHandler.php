<?php
	declare(strict_types = 1);

	namespace Edde\Common\Event\Handler;

	use Edde\Api\Event\EventException;
	use Edde\Api\Event\IEvent;
	use Edde\Common\Event\AbstractHandler;
	use Edde\Common\Usable\UsableTrait;

	/**
	 * This should take instance on input and return all methods accepting exactly one IEvent parameter.
	 */
	class ReflectionHandler extends AbstractHandler {
		use UsableTrait;
		protected $handler;

		protected $methodList = [];

		public function __construct($handler) {
			$this->handler = $handler;
		}

		public function getIterator() {
			$this->use();
			return new \ArrayIterator($this->methodList);
		}

		protected function prepare() {
			$reflectionClass = new \ReflectionClass($this->handler);
			foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
				if ($reflectionMethod->getNumberOfParameters() !== 1) {
					continue;
				}
				/** @var $reflectionParameter \ReflectionParameter */
				$reflectionParameter = $reflectionMethod->getParameters()[0];
				if (($class = $reflectionParameter->getClass()) === null) {
					continue;
				}
				if (in_array(IEvent::class, $class->getInterfaceNames(), true) === false) {
					continue;
				}
				if (isset($this->methodList[$event = $class->getName()])) {
					throw new EventException(sprintf('Event class [%s] was already registered in handler [%s].', $event, $reflectionClass->getName()));
				}
				$this->methodList[$event] = $reflectionMethod->getClosure($this->handler);
			}
		}
	}
